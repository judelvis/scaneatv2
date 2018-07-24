<?php

class Plates extends Result{

    var $plates;

    function setPlates($s) {
        $this->plates = $s;
    }

    function getPlates() {
        return $this->plates;
    }

    static function getPlatesById($db, $plate_id) {
        $result = new Plates();
        $sql="select r.nombre as rest_name, p.*, pstats.presentacion, 
        pstats.calidadprecio, pstats.sabortextura, pstats.valoraciones, ps.seccion_id as seccion_carta_id
        from platos p 
        left join platossecciones ps on ps.plato_id = p.id 
        left join plato_stats pstats on pstats.id_plato = p.id
        inner join restaurantes r on r.id = p.rest_id 
        where p.id = ".$plate_id;
        $query = $db->prepare($sql);
        $query->execute();
        $num_rows_userExist = $query->rowCount();
        if($num_rows_userExist > 0) {
            $plates = Plates::parsePlates($db,$query);
            $result->setStatus(OK);
            $result->setMessage("");
            $result->setPlates($plates);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos de los platos");
        }

        return $result;
    }

    static function getPlatesByRestaurantId($db, $rest_id) {
        $result = new Plates();
        $sql="select r.nombre as rest_name, p.*, pstats.presentacion, 
        pstats.calidadprecio, pstats.sabortextura, pstats.valoraciones, ps.seccion_id as seccion_carta_id
        from platos p 
        left join platossecciones ps on ps.plato_id = p.id
        left join plato_stats pstats on pstats.id_plato = p.id
	    inner join restaurantes r on r.id = p.rest_id 
        where p.rest_id = ".$rest_id;
        $query = $db->prepare($sql);
        $query->execute();
        $num_rows_userExist = $query->rowCount();
        if($num_rows_userExist > 0) {
            $plates = Plates::parsePlates($db,$query);
            $result->setStatus(OK);
            $result->setMessage("");
            $result->setPlates($plates);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos de los platos");
        }

        return $result;
    }

    static function getBestPlatesByTown($db, $town, $start, $size) {
        $result = new Plates();
        $sql="select r.nombre as rest_name, p.*, pstats.presentacion, 
        pstats.calidadprecio, pstats.sabortextura, pstats.valoraciones, 
        ((pstats.presentacion + pstats.calidadprecio + pstats.sabortextura) / 3) as avg,
        ((pstats.positivos + 1.9208) / (pstats.positivos + pstats.negativos) -
        1.96 * SQRT((pstats.positivos * pstats.negativos) / (pstats.positivos + pstats.negativos) + 0.9604) /
        (pstats.positivos + pstats.negativos)) / (1 + 3.8416 / (pstats.positivos + pstats.negativos))
        AS ci_lower_bound
        from platos p 
        left join platossecciones ps on ps.plato_id = p.id
        left join plato_stats pstats on pstats.id_plato = p.id
	    inner join restaurantes r on r.id = p.rest_id 
        where r.town='".$town."'
        order by ci_lower_bound DESC, avg DESC, pstats.valoraciones DESC, p.id DESC
        LIMIT ".$start.",".$size;
        $query = $db->prepare($sql);
        $query->execute();
        $num_rows_userExist = $query->rowCount();
        if($num_rows_userExist > 0) {
            $plates = Plates::parsePlates($db,$query);
            $result->setStatus(OK);
            $result->setMessage("");
            $result->setPlates($plates);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos de los platos");
        }

        return $result;
    }

    static function parsePlates($db,$query) {
        $plates = array();
        $index = 1;
        while($row=$query->fetch()){
            $id = $row["id"];
            $plates[$index] = $row;
            $ingredients = Ingredient::getIngredientsByPlate($db,$id)->getIngredients();
            $plates[$index]["ingredients"] = $ingredients;
            $allergens = Allergens::getAllergensByPlate($db,$id)->getAllergens();
            $plates[$index]["allergens"] = $allergens;
            $foodType = FoodType::getFoodTypeByPlate($db,$id)->getFoodType();
            $plates[$index]["foodType"] = $foodType;
            $reviews = Review::getReviewsPlatePaged($db,$id,0,10)->getReview();
            $plates[$index]["reviews"] = $reviews;
            $plates[$index]["stats"] = Review::getReviewStatsByPlateId($db, $id);
            $plates[$index]["images"] = getPlateImage($id);
            $index = $index + 1;
        }
        return $plates;
    }
}