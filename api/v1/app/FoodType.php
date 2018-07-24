<?php

define('ERROR_LOADING_FOOD_TYPE', 2020);
class FoodType extends Result{
	var $foodType;

	function setFoodType($s) {
		$this->foodType = $s;
	}
	
	function getFoodType() {
		return $this->foodType;
	}

    static function getFoodTypeByPlate($db, $plateId){
		$result = new FoodType();
        $sql = "SELECT cp.id_categoria as id, c.name as food_type FROM categoriasplatos cp
        INNER JOIN categories c ON c.id = cp.id_categoria
        where cp.id_platos = ".$plateId."";
        $query = $db->prepare($sql);
        $query->execute();
        $num_rows_userExist = $query->rowCount();
        if($num_rows_userExist > 0) {
            $list = array();
            while($row=$query->fetch()){
                $list[$row["id"]] = $row["food_type"];
            }
            $result->setStatus(OK);
            $result->setMessage("");
            $result->setFoodType($list);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos del tipo de comida");
        }
		return $result;
	}
    
    static function getFoodTypeByRestaurant($db, $restId){
		$result = new FoodType();
        $sql = "SELECT cr.id_categoria as id, c.name as food_type FROM categoriasrestaurante cr
        INNER JOIN categories c ON c.id = cr.id_categoria
        where cr.id_restaurante = ".$restId."";
        $query = $db->prepare($sql);
        $query->execute();
        $num_rows_userExist = $query->rowCount();
        if($num_rows_userExist > 0) {
            $list = array();
            while($row=$query->fetch()){
                $list[$row["id"]] = $row["food_type"];
            }
            $result->setStatus(OK);
            $result->setMessage("");
            $result->setFoodType($list);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos del tipo de comida");
        }
		return $result;
	}
    
    static function getFoodTypeEditByRestaurant($db, $restId){
        $result = new FoodType();
        
        $sql = "SELECT c.id, c.name as name, cr.id_categoria as checked FROM categories c
        LEFT JOIN (SELECT id_categoria FROM categoriasrestaurante
        where id_restaurante = ".$restId.") cr ON c.id = cr.id_categoria";
        
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
            $result->setFoodType($list);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos del tipo de comida");
        }
		return $result;
    }
    
    static function updateRestaurant($db,$rest_id, $foodType) {
        $sql_delete = "delete from categoriasrestaurante where id_restaurante = ".$rest_id;
        $query = $db->prepare($sql_delete);
        $query->execute();

        $values = "VALUES ";
        foreach ($foodType as $key => $value) {
            $values .= "(".$rest_id.",".$value."),";
        }
        if ($values != "VALUES ") {
            $values=rtrim($values,",");
            $sql_update = "INSERT INTO categoriasrestaurante (id_restaurante, id_categoria) ".$values;
            $query = $db->prepare($sql_update);
            $query->execute();
        }
    }
}