<?php

define('ERROR_LOADING_ALLERGENS', 2010);
class Allergens extends Result{
	var $allergens;

	function setAllergens($s) {
		$this->allergens = $s;
	}
	
	function getAllergens() {
		return $this->allergens;
	}

    static function getAllergensByPlate($db, $plateId){
		$result = new Allergens();
        $sql = "SELECT pa.alergeno_id as id, a.name_image as allergen FROM platosalergenos pa
        INNER JOIN alergenos a ON a.id = pa.alergeno_id
        where pa.plato_id = ".$plateId;
        $query = $db->prepare($sql);
        $query->execute();
        $num_rows_userExist = $query->rowCount();
        if($num_rows_userExist > 0) {
            $list = array();
            while($row=$query->fetch()){
                $list[$row["id"]] = $row["allergen"];
            }
            $result->setStatus(OK);
            $result->setMessage("");
            $result->setAllergens($list);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos de los alérgenos");
        }
		return $result;
    }
    
    static function getAllergensByUserId($db, $userId){
        $sql = "SELECT id_alergeno FROM usuarioalergenos
        where id_usuario = ".$userId;
        $query = $db->prepare($sql);
        $query->execute();
        $num_rows_userExist = $query->rowCount();
        if($num_rows_userExist > 0) {
            $list = array();
            while($row=$query->fetch()){
                $list[$row["id_alergeno"]] = $row["id_alergeno"];
            }
        }
		return $list;
	}
}