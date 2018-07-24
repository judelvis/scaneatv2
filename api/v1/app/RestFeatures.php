<?php

define('ERROR_LOADING_REST_FEATURES', 2040);
class RestFeatures extends Result{
	var $restFeatures;

	function setRestFeatures($s) {
		$this->restFeatures = $s;
	}
	
	function getRestFeatures() {
		return $this->restFeatures;
	}

    static function getRestFeaturesByRestaurantId($db, $restId){
		$result = new RestFeatures();
        $sql = "SELECT c.id, c.caracteristica as name FROM caracteristicasrest cr
        INNER JOIN caracteristicaslocal c ON c.id = cr.caracteristica_id
        where cr.rest_id = ".$restId."";
        $query = $db->prepare($sql);
        $query->execute();
        $num_rows_userExist = $query->rowCount();
        if($num_rows_userExist > 0) {
            $list = array();
            while($row=$query->fetch()){
                $list[$row["id"]] = $row["name"];
            }
            $result->setStatus(OK);
            $result->setMessage("");
            $result->setRestFeatures($list);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos de las características del restaurante");
        }
		return $result;
    }
    
    static function getRestFeaturesEditByRestaurantId($db, $restId){
		$result = new RestFeatures();
        $sql = "SELECT c.id, c.caracteristica as name, cr.caracteristica_id as checked FROM caracteristicaslocal c
        LEFT JOIN (SELECT caracteristica_id FROM caracteristicasrest
        where rest_id = ".$restId.") cr ON c.id = cr.caracteristica_id";
        
        $query = $db->prepare($sql);
        $query->execute();
        $num_rows_userExist = $query->rowCount();
        if($num_rows_userExist > 0) {
            $list = array();
            while($row=$query->fetch()){
                $id = $row["id"];
                unset($row["id"]);
                $list[$id] = $row;
            }
            $result->setStatus(OK);
            $result->setMessage("");
            $result->setRestFeatures($list);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos de las características del restaurante");
        }
		return $result;
	}
    
    static function updateRestaurant($db,$rest_id, $restFeatures) {
        $sql_delete = "delete from caracteristicasrest where rest_id = ".$rest_id;
        $query = $db->prepare($sql_delete);
        $query->execute();

        $values = "VALUES ";
        foreach ($restFeatures as $key => $value) {
            $values .= "(".$rest_id.",".$value."),";
        }
        if ($values != "VALUES ") {
            $values=rtrim($values,",");
            $sql_update = "INSERT INTO caracteristicasrest (rest_id, caracteristica_id) ".$values;
            $query = $db->prepare($sql_update);
            $query->execute();
        }
    }
}