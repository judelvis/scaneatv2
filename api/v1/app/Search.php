<?php

class Search extends Result{
    var $search;

    function setSearch($s) {
        $this->search = $s;
    }

    function getSearch() {
        return $this->search;
    }
    static function DoSearchText($db, $text, $searchFilter, $isPlate, $start = 0, $size = 10) {
        $location = $searchFilter["location"];
        $search_exploded = explode ( " ", $text );
        $x = 0;
        $where = "where";
        foreach( $search_exploded as $search_each ) {
            $x++;
            if( $x != 1 ) {
                $where .= "OR ";
            }
            $where .= " nombre LIKE '%".$search_each."%' ";
        }
        $sql = Search::GetLocationSQL($location, $isPlate, $where);

        echo $sql;
        /*$query = $db->prepare($sql);
            $query->execute();
            $num_rows_userExist = $query->rowCount();
            if($num_rows_userExist > 0) {
                if ($isPlate) {
                    $plates = Plates::parsePlates($db,$query);
                    $result->setPlates($plates);
                } else {
                    $restaurant = Restaurants::parseRestaurant($db,$query);
                    $result->setRestaurants($restaurant);
                }
                $result->setStatus(OK);
                $result->setMessage("");
            } else {
                $result->setStatus(VOID);
                $result->setMessage("No se han podido cargar los restaurantes");
            }*/
        $result->setStatus(OK);
        $result->setMessage($sql);
        echo $sql;
        return $result;
    }
    static function DoSearch($db, $searchFilter, $isPlate, $start = 0, $size = 10) {
        $specialSearch = $searchFilter["specialSearch"];
        $foodType = $searchFilter["foodType"];
        $location = $searchFilter["location"];
        $vegan = $searchFilter["vegan"];
        $vegetarian = $searchFilter["vegetarian"];
        $wheat = $searchFilter["wheat"];
        $allergens = $searchFilter["allergens"];
        $ingredients = $searchFilter["ingredients"];
        $noIngredients = $searchFilter["noIngredients"];
        $minPrice = $searchFilter["minPrice"];
        $maxPrice = $searchFilter["maxPrice"];

        /*$location = json_decode($location,true);
        $foodType = json_decode($foodType, true);
        $allergens = json_decode($allergens, true);
        $ingredients = json_decode($ingredients, true);
        $noIngredients = json_decode($noIngredients, true);*/
        $sql = array();

        if ($isPlate) {
            $result = new Plates();
        } else {
            $result = new Restaurants();
        }
        if ($location) {
            $locationSql = Search::GetLocationSQL($location, $isPlate);
            /*if ($specialSearch) {
                if ($specialSearch == "newestRest") {
                    $specialSearchSql = "select id as rest_id 
                    from restaurantes
                    order by createDate DESC";
                } else if ($specialSearch == "bestMenu") {
                    $specialSearchSql = "select r.id as rest_id, (rs.presentacion + rs.calidadprecio + rs.sabortextura)/3 as avg
                    from restaurantes r inner join restaurante_stats rs ON r.id = rs.id_rest
                    order by avg DESC";
                } else if ($specialSearch == "promoted") {
                    $specialSearchSql = "select id as rest_id 
                    from restaurantes
                    where destacado = 1";
                }
                if ($specialSearchSql)
                    $sql["specialSearch"] = $specialSearchSql;
            }*/
            if ($foodType) {
                $sql["foodType"] = Search::GetFoodTypeSQL($foodType, $isPlate);
            }
            if ($vegan && $vegan == 1) {
                $sql["vegan"] = Search::GetVeganSQL($isPlate);
            }
            if ($vegetarian && $vegetarian == 1) {
                $sql["vegetarian"] = Search::GetVegetarianSQL($isPlate);
            }
            if ($wheat && $wheat == 1) {
                $sql["wheat"] = Search::GetWheatSQL($isPlate);
            }
            if ($allergens) {
                $sql["allergens"] = Search::GetAllergens($allergens, $isPlate);
            }
            if ($ingredients) {
                $sql["ingredients"] = Search::GetIngredientsSQL($ingredients,$isPlate);
            }
            if ($noIngredients) {
                $sql["noIngredients"] = Search::GetIngredientsSQL($noIngredients,$isPlate);
            }
            if ($minPrice || $maxPrice) {
                $sql["price"] = Search::GetPriceSQL($minPrice, $maxPrice, $isPlate);
            }

            $finalSql = Search::JoinSQLs($locationSql, $sql, $start, $size, $isPlate);

            $query = $db->prepare($finalSql);
            $query->execute();
            $num_rows_userExist = $query->rowCount();
            if($num_rows_userExist > 0) {
                if ($isPlate) {
                    $plates = Plates::parsePlates($db,$query);
                    $result->setPlates($plates);
                } else {
                    $restaurant = Restaurants::parseRestaurant($db,$query);
                    $result->setRestaurants($restaurant);
                }
                $result->setStatus(OK);
                $result->setMessage($finalSql);
            } else {
                $result->setStatus(VOID);
                $result->setMessage("No se han podido cargar los restaurantes".$finalSql);
            }
        } else {
            $result->setStatus(LOCATION_EMPTY_RESTAURANTS);
            $result->setMessage("Se necesita al menos la localización");
        }
        return $result;
    }

    static function GetLocationSQL($location, $isPlate, $where = "") {
        $userLat = $location["lat"];
        $userLon = $location["lon"];
        $radio = $location["radio"];
        if ($isPlate) {
            $sql = "SELECT r.nombre as rest_name, p.*, pstats.presentacion,
            pstats.calidadprecio, pstats.sabortextura, pstats.valoraciones, ps.seccion_id as seccion_carta_id,
            ( 6371 * acos( cos( radians(".$userLat.") ) * cos( radians( r.latitud ) ) 
            * cos( radians( r.longitud ) - radians(".$userLon.") ) + sin( radians(".$userLat.") ) * sin(radians(r.latitud)) ) ) AS distance 
            FROM platos p
            LEFT JOIN platossecciones ps on ps.plato_id = p.id 
            INNER JOIN restaurantes r ON r.id = p.rest_id
            LEFT JOIN plato_stats pstats ON pstats.id_plato = p.id " . $where . "
            HAVING distance < ".$radio."
            ORDER BY distance";
        } else {
            $sql = "SELECT r.*, rs.presentacion, rs.calidadprecio, rs.sabortextura, rs.valoraciones, 
            ( 6371 * acos( cos( radians(".$userLat.") ) * cos( radians( r.latitud ) ) 
            * cos( radians( r.longitud ) - radians(".$userLon.") ) + sin( radians(".$userLat.") ) * sin(radians(r.latitud)) ) ) AS distance 
            FROM restaurantes r
            LEFT JOIN restaurante_stats rs ON rs.id_rest = r.id " . $where . "
            HAVING distance < ".$radio."
            ORDER BY distance";
        }
        return $sql;
    }

    static function GetIngredientsSQL($ingredients, $isPlate) {
        $where="";
        foreach ($ingredients as $value) {
            if ($where != "") {
                $where .= " OR ";
            }
            $where .= "pi.id_ingrediente = " .$value;
        }
        if ($where != "") {
            if ($isPlate) {
                $sql = "select p.id
                    from platosingredientes pi 
                    inner join platos p on pi.id_plato=p.id
                    where ".$where."
                    group by p.id";
            } else {
                $sql = "select * from (
                    select p.rest_id
                    from platosingredientes pi 
                    inner join platos p on pi.id_plato=p.id
                    where ".$where."
                    group by p.id) t1
                    group by rest_id";
            }
        }
        return $sql;
    }

    static function GetFoodTypeSQL($foodType, $isPlate) {
        /*$where = "";
        foreach ($foodType as $value) {
            if ($where != "") {
                $where .= " OR ";
            }
            $where .= "c.id_categoria = " .$value;
        }

        if ($where != "") {
            if ($isPlate) {
                $sql = "select p.id 
                from categoriasplatos c
                inner join platos p on c.id_platos = p.id
                where ".$where."
                group by c.id_platos";
            } else {
                $sql = "select *
                from 
                    (select p.rest_id 
                    from categoriasplatos c
                    inner join platos p on c.id_platos = p.id
                    where ".$where."
                    group by c.id_platos) categorias
                group by rest_id";
            }
        }
        return $sql;*/
        $where = "";
        foreach ($foodType as $value) {
            if ($where != "") {
                $where .= " OR ";
            }
            $where .= "c.id_categoria = " .$value;
        }

        if ($where != "") {
            if ($isPlate) {
                $sql = "select p.id 
                from categoriasplatos c
                inner join platos p on c.id_platos = p.id
                where ".$where."
                group by c.id_platos";
            } else {
                $sql = "select * from (select p.id as rest_id  from categoriasrestaurante as c                    
	              inner join restaurantes p on c.id_restaurante = p.id
	where ".$where." 
	group by c.id_restaurante) categorias                
	group by rest_id ";
            }
        }
        return $sql;
    }

    static function GetVeganSQL($isPlate) {
        if ($isPlate) {
            $sql = "select id 
            from platos 
            where vegano = true";
        } else {
            $sql = "select rest_id 
            from platos 
            where vegano = true group by rest_id";
        }
        return $sql;
    }

    static function GetVegetarianSQL($isPlate) {
        if ($isPlate) {
            $sql = "select id 
            from platos 
            where vegetariano = true";
        } else {
            $sql = "select rest_id 
            from platos 
            where vegetariano = true group by rest_id";
        }
        return $sql;
    }

    static function GetWheatSQL($isPlate) {
        if ($isPlate) {
            $sql = "select id 
            from platos 
            where celiaco = true";
        } else {
            $sql = "select rest_id 
            from platos 
            where celiaco = true group by rest_id";
        }
        return $sql;
    }

    static function GetAllergens($allergens, $isPlate) {
        $where="";
        $count = 0;
        foreach ($allergens as $value) {
            if ($where != "") {
                $where .= " OR ";
            }
            $where .= "pa.alergeno_id = " .$value;
            $count += 1;
        }
        if ($where != "") {
            if ($isPlate) {
                $sql = "select p.id
                from platosalergenos pa 
                inner join platos p on pa.plato_id=p.id
                where ".$where."
                group by p.id";
            } else {
                $sql = "select * from (select p.rest_id
                from platosalergenos pa 
                inner join platos p on pa.plato_id=p.id
                where ".$where."
                group by p.id) t1
                group by rest_id";
            }
        }
        return $sql;
    }

    static function GetPriceSQL($minPrice, $maxPrice, $isPlate) {
        if ($isPlate) {
            $priceSql = "select id from platos where ";
        } else {
            $priceSql = "select rest_id from platos where ";
        }
        if ($minPrice != -1) {
            $minPriceSql = "precio > ".$minPrice;
            $priceSql .= $minPriceSql;
        }
        if ($maxPrice != -1) {
            $maxPriceSql = "precio < ".$maxPrice;
            if ($minPrice != -1) {
                $priceSql .= " AND ";
            }
            $priceSql .= $maxPriceSql;
        }

        if ($priceSql != "select id from platos where "
            && $priceSql != "select rest_id from platos where ") {
            $sql = $priceSql;
            if (!$isPlate) {
                $sql .= " group by rest_id";
            }
        }

        return $sql;
    }

    static function JoinSQLs($locationSql, $sql, $start, $size, $isPlate) {
        $finalSql = "select location.* from (".$locationSql.") as location ";
        $leftJoinWhere = " where ";
        foreach ($sql as $key => $value) {
            if ($value) {
                if ($key == "noIngredients" || $key == "allergens") {
                    $finalSql .= " left join (".$value.") as ".$key;
                    if ($leftJoinWhere != " where ") {
                        $leftJoinWhere .= " AND ";
                    }
                    if ($isPlate) {
                        $leftJoinWhere .= $key.".id IS NULL ";
                    } else  {
                        $leftJoinWhere .= $key.".rest_id IS NULL ";
                    }
                } else {
                    $finalSql .= " inner join (".$value.") as ".$key;
                }
                if ($isPlate) {
                    $finalSql .= " on location.id = ".$key.".id ";
                } else  {
                    $finalSql .= " on location.id = ".$key.".rest_id ";
                }
            }
        }
        if ($leftJoinWhere != " where ") {
            $finalSql .= $leftJoinWhere;
        }
        $finalSql .= " ORDER BY distance asc 
            LIMIT ".$start.",".$size;

        return $finalSql;
    }
}