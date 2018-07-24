<?php

define('ERROR_LOADING_REST_SERVICES', 2040);
class RestServices extends Result{
	var $restServices;

	function setRestServices($s) {
		$this->restServices = $s;
	}
	
	function getRestServices() {
		return $this->restServices;
	}

    static function getRestServicesByRestaurantId($db, $restId){
		$result = new RestServices();
        $sql = "SELECT s.id, s.servicio as name FROM tiposerviciosrest sr
        INNER JOIN tiposervicios s ON s.id = sr.servicio_id
        where sr.rest_id = ".$restId."";
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
            $result->setRestServices($list);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos de los servicios del restaurante");
        }
		return $result;
    }
    
    static function getRestServicesEditByRestaurantId($db, $restId){
		$result = new RestServices();

        $sql = "SELECT s.id, s.servicio as name, sr.servicio_id as checked FROM tiposervicios s
        LEFT JOIN (SELECT servicio_id FROM tiposerviciosrest
        where rest_id = ".$restId.") sr ON s.id = sr.servicio_id";

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
            $result->setRestServices($list);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos de los servicios del restaurante");
        }
		return $result;
	}
    
    static function updateRestaurant($db,$rest_id, $restServices) {
        $sql_delete = "delete from tiposerviciosrest where rest_id = ".$rest_id;
        $query = $db->prepare($sql_delete);
        $query->execute();

        $values = "VALUES ";
        foreach ($restServices as $key => $value) {
            $values .= "(".$rest_id.",".$value."),";
        }
        if ($values != "VALUES ") {
            $values=rtrim($values,",");
            $sql_update = "INSERT INTO tiposerviciosrest (rest_id, servicio_id) ".$values;
            $query = $db->prepare($sql_update);
            $query->execute();
        }
    }
    
}