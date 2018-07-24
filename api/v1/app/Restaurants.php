<?php

class Restaurants extends Result{

    var $restaurants;

    function setRestaurants($s) {
        $this->restaurants = $s;
    }

    function getRestaurants() {
        return $this->restaurants;
    }

    static function getRestaurantById($db, $rest_id) {
        $result = new Restaurants();

        $sql="select r.*, rs.presentacion, rs.calidadprecio, rs.sabortextura, rs.valoraciones from restaurantes r 
        left join restaurante_stats rs on rs.id_rest = r.id
        where id = ".$rest_id;
        $query = $db->prepare($sql);
        $query->execute();
        $num_rows_userExist = $query->rowCount();
        if($num_rows_userExist > 0) {
            $restaurant = Restaurants::parseRestaurant($db,$query);
            $result->setStatus(OK);
            $result->setMessage("");
            $result->setRestaurants($restaurant);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos del restaurante");
        }
        return $result;
    }

    static function getRestaurantToEditById($db, $rest_id) {
        $result = new Restaurants();

        $sql="select id, nombre, direccion, descripcion, website, facebook, twitter from restaurantes 
        where id = ".$rest_id;
        $query = $db->prepare($sql);
        $query->execute();
        $num_rows_userExist = $query->rowCount();
        $restaurant = null;
        if($num_rows_userExist > 0) {
            while($row=$query->fetch()){
                $restaurant = $row;
                $foodType = FoodType::getFoodTypeEditByRestaurant($db,$rest_id)->getFoodType();
                $restaurant["foodType"] = $foodType;
                $restFeatures = RestFeatures::getRestFeaturesEditByRestaurantId($db,$rest_id)->getRestFeatures();
                $restaurant["restFeatures"] = $restFeatures;
                $restEvents = RestEvents::getRestEventsEditByRestaurantId($db,$rest_id)->getRestEvents();
                $restaurant["restEvents"] = $restEvents;
                $restServices = RestServices::getRestServicesEditByRestaurantId($db,$rest_id)->getRestServices();
                $restaurant["restServices"] = $restServices;
                $restSchedule = RestSchedule::getRestScheduleByRestaurantId($db,$rest_id)->getRestSchedule();
                $restaurant["restSchedule"] = $restSchedule;
                $telephones = Telephone::getTelephonesByRestaurantId($db,$rest_id)->getTelephone();
                $restaurant["telephones"] = $telephones;
            }
            $result->setStatus(OK);
            $result->setMessage("");
            $result->setRestaurants($restaurant);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos del restaurante");
        }
        return $result;
    }

    static function updateRestaurant($db, $restaurant) {
        $rest_id = $restaurant["id"];
        $result = new Restaurants();
        $sql = "update restaurantes set nombre='".$restaurant["nombre"]."',
        descripcion='".$restaurant["descripcion"]."',
        website='".$restaurant["website"]."',
        facebook='".$restaurant["facebook"]."',
        twitter='".$restaurant["twitter"]."'
        where id = ".$rest_id;
        $query = $db->prepare($sql);
        $query->execute();
        if ($restaurant["foodType"]) {
            FoodType::updateRestaurant($db,$rest_id, $restaurant["foodType"]);
        }
        if ($restaurant["restFeatures"]) {
            RestFeatures::updateRestaurant($db,$rest_id, $restaurant["restFeatures"]);
        }
        if ($restaurant["restEvents"]) {
            RestEvents::updateRestaurant($db,$rest_id, $restaurant["restEvents"]);
        }
        if ($restaurant["restServices"]) {
            RestServices::updateRestaurant($db,$rest_id, $restaurant["restServices"]);
        }
        if ($restaurant["restSchedule"]) {
            RestSchedule::updateRestaurant($db,$rest_id, $restaurant["restSchedule"]);
        }
        if ($restaurant["telephones"]) {
            Telephone::updateRestaurant($db,$rest_id, $restaurant["telephones"]);
        }

        $result->setStatus(OK);
        $result->setMessage("");
        return $result;
    }

    static function getBestRestaurantsByTown($db, $town, $start, $size) {
        $result = new Restaurants();
        $sql="select r.*, rs.presentacion, rs.calidadprecio, rs.sabortextura, rs.valoraciones,
        ((rs.presentacion + rs.calidadprecio + rs.sabortextura) / 3) as avg,
        ((rs.positivos + 1.9208) / (rs.positivos + rs.negativos) -
        1.96 * SQRT((rs.positivos * rs.negativos) / (rs.positivos + rs.negativos) + 0.9604) /
        (rs.positivos + rs.negativos)) / (1 + 3.8416 / (rs.positivos + rs.negativos))
        AS ci_lower_bound
        from restaurantes r 
        inner join restaurante_stats rs ON r.id = rs.id_rest
        where r.town='".$town."'
        order by ci_lower_bound DESC, avg DESC, rs.valoraciones DESC, r.id DESC
        LIMIT ".$start.",".$size;
        $query = $db->prepare($sql);
        $query->execute();
        $num_rows_userExist = $query->rowCount();
        if($num_rows_userExist > 0) {
            $restaurant = Restaurants::parseRestaurant($db,$query);
            $result->setStatus(OK);
            $result->setMessage("");
            $result->setRestaurants($restaurant);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos de los restaurantes");
        }
        return $result;
    }

    static function getNewestRestaurantsByTown($db, $town, $start, $size) {
        $result = new Restaurants();
        $sql="select r.*, rs.presentacion, rs.calidadprecio, rs.sabortextura, rs.valoraciones 
        from restaurantes r
        left join restaurante_stats rs on rs.id_rest = r.id
        where town='".$town."'
        order by createDate DESC
        LIMIT ".$start.",".$size;
        $query = $db->prepare($sql);
        $query->execute();
        $num_rows_userExist = $query->rowCount();
        if($num_rows_userExist > 0) {
            $restaurant = Restaurants::parseRestaurant($db,$query);
            $result->setStatus(OK);
            $result->setMessage("");
            $result->setRestaurants($restaurant);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos de los restaurantes");
        }
        return $result;
    }

    static function getPromotedRestaurantsByTown($db, $town, $start, $size) {
        $result = new Restaurants();
        $sql="select r.*, rs.presentacion, rs.calidadprecio, rs.sabortextura, rs.valoraciones 
        from restaurantes r
        left join restaurante_stats rs on rs.id_rest = r.id
        where destacado = 1 AND town='".$town."' 
        LIMIT ".$start.",".$size;
        $query = $db->prepare($sql);
        $query->execute();
        $num_rows_userExist = $query->rowCount();
        echo "\n\nnumrows:".$num_rows_userExist."\n\n";
        if($num_rows_userExist > 0) {
            $restaurant = Restaurants::parseRestaurant($db,$query);
            $result->setStatus(OK);
            $result->setMessage("");
            $result->setRestaurants($restaurant);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos de los restaurantes");
        }
        return $result;
    }

    static function parseRestaurant($db,$query) {
        $restaurant = array();
        $index = 1;
        while($row=$query->fetch()){
            $id = $row["id"];
            $plates = Plates::getPlatesByRestaurantId($db,$id);
            $menu = Menu::getMenuByRestaurantId($db,$id);
            $restaurant[$index] = $row;
            $foodType = FoodType::getFoodTypeByRestaurant($db,$id)->getFoodType();
            $restaurant[$index]["foodType"] = $foodType;
            $restFeatures = RestFeatures::getRestFeaturesByRestaurantId($db,$id)->getRestFeatures();
            $restaurant[$index]["restFeatures"] = $restFeatures;
            $restEvents = RestEvents::getRestEventsByRestaurantId($db,$id)->getRestEvents();
            $restaurant[$index]["restEvents"] = $restEvents;
            $restServices = RestServices::getRestServicesByRestaurantId($db,$id)->getRestServices();
            $restaurant[$index]["restServices"] = $restServices;
            $restSchedule = RestSchedule::getRestScheduleByRestaurantId($db,$id)->getRestSchedule();
            $restaurant[$index]["restSchedule"] = $restSchedule;
            $telephones = Telephone::getTelephonesByRestaurantId($db,$id)->getTelephone();
            $restaurant[$index]["telephones"] = $telephones;
            $restaurant[$index]["plates"] = $plates->getPlates();
            $restaurant[$index]["stats"] = Review::getReviewStatsByRestaurantId($db,$id);
            $restaurant[$index]["menu"] = $menu->getMenu();
            $index = $index + 1;
        }
        return $restaurant;
    }
}