<?php

class Telephone extends Result {
	var $telephone;
	function setTelephone($s) {
		$this->telephone = $s;
	}
	function getTelephone() {
		return $this->telephone;
	}
	static function getTelephonesByRestaurantId($db,$restId){
		$result = new Telephone();
		if ($restId) {
			$sql = "select telephone from telephones where id_restaurante = ".$restId;
			$query = $db->prepare($sql);
			$query->execute();

			$telephones = array();
			while($row=$query->fetch()){
				array_push($telephones,$row["telephone"]);
			}

			$result->setStatus(OK);
			$result->setMessage("");
			$result->setTelephone($telephones);
		} else {
			$result->setStatus(REST_ID_EMPTY);
			$result->setMessage("Restaurant id empty");
		}
		return $result;
	}
    
    static function updateRestaurant($db,$rest_id, $telephones) {
        $sql_delete = "delete from telephones where id_restaurante = ".$rest_id;
        $query = $db->prepare($sql_delete);
        $query->execute();

        $values = "VALUES ";
        foreach ($telephones as $key => $value) {
			if ($value) {
				$values .= "(".$rest_id.",".$value."),";
			}
		}
        if ($values != "VALUES ") {
            $values=rtrim($values,",");
            $sql_update = "INSERT INTO telephones (id_restaurante, telephone) ".$values;
            $query = $db->prepare($sql_update);
            $query->execute();
        }
    }
}
?>