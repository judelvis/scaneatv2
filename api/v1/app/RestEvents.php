<?php

define('ERROR_LOADING_REST_EVENTS', 2030);
class RestEvents extends Result{
	var $restEvents;

	function setRestEvents($s) {
		$this->restEvents = $s;
	}
	
	function getRestEvents() {
		return $this->restEvents;
	}

    static function getRestEventsByRestaurantId($db, $restId){
		$result = new RestEvents();
        $sql = "SELECT er.evento_id as id, e.name as name FROM eventoslocalrest er
        INNER JOIN eventoslocal e ON e.id = er.evento_id
        where er.rest_id = ".$restId."";
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
            $result->setRestEvents($list);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos de los eventos del restaurante");
        }
		return $result;
	}

    static function getRestEventsEditByRestaurantId($db, $restId){
		$result = new RestEvents();

        $sql = "SELECT e.id, e.name as name, er.evento_id as checked FROM eventoslocal e
        LEFT JOIN (SELECT evento_id FROM eventoslocalrest
        where rest_id = ".$restId.") er ON e.id = er.evento_id";

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
            $result->setRestEvents($list);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos de los eventos del restaurante");
        }
		return $result;
	}
    
    static function updateRestaurant($db,$rest_id, $restEvents) {
        $sql_delete = "delete from eventoslocalrest where rest_id = ".$rest_id;
        $query = $db->prepare($sql_delete);
        $query->execute();

        $values = "VALUES ";
        foreach ($restEvents as $key => $value) {
            $values .= "(".$rest_id.",".$value."),";
        }
        if ($values != "VALUES ") {
            $values=rtrim($values,",");
            $sql_update = "INSERT INTO eventoslocalrest (rest_id, evento_id) ".$values;
            $query = $db->prepare($sql_update);
            $query->execute();
        }
    }
    
}