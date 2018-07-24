<?php

class Ingredient extends Result{
	var $ingredients;

	function setIngredients($s) {
		$this->ingredients = $s;
	}
	
	function getIngredients() {
		return $this->ingredients;
	}

	static function getAllIngredients($db){
		$result = new Ingredient();
        $sql="SELECT * FROM ingredientes";
        $query = $db->prepare($sql);
        $query->execute();
        $num_rows_userExist = $query->rowCount();
        if($num_rows_userExist > 0) {
            $list = array();
            while($row=$query->fetch()){
                $id = $row['id'];
                $name = $row['name'];
                $name_search = $row['name_search'];
                $item = array('id' => $row['id'],
                    'name' => $row['name'],
                    'name_search' => $row['name_search'] );
                array_push($list,$item);
            }
            $result->setStatus(OK);
            $result->setMessage("");
            $result->setIngredients($list);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos de los ingredientes");
        }
		return $result;
	}
    
    static function getIngredientsBy($db, $string){
		$result = new Ingredient();
        $sql="SELECT * FROM ingredientes name_search where name_search like \"".$string."%s\"";
        $query = $db->prepare($sql);
        $query->execute();
        $num_rows_userExist = $query->rowCount();
        if($num_rows_userExist > 0) {
            $list = array();
            while($row=$query->fetch()){
                $id = $row['id'];
                $name = $row['name'];
                $name_search = $row['name_search'];
                $item = array('id' => $row['id'],
                    'name' => $row['name'],
                    'name_search' => $row['name_search'] );
                array_push($list,$item);
            }
            $result->setStatus(OK);
            $result->setMessage("");
            $result->setIngredients($list);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos de los ingredientes");
        }
		return $result;
	}

    static function getIngredientsByPlate($db, $plateId){
		$result = new Ingredient();
        $sql = "SELECT i.id, i.name as ingredient FROM platosingredientes pi
        INNER JOIN ingredientes i ON i.id = pi.id_ingrediente
        where pi.id_plato = ".$plateId."";
        $query = $db->prepare($sql);
        $query->execute();
        $num_rows_userExist = $query->rowCount();
        if($num_rows_userExist > 0) {
            $list = array();
            while($row=$query->fetch()){
                $list[$row["id"]] = $row["ingredient"];
            }
            $result->setStatus(OK);
            $result->setMessage("");
            $result->setIngredients($list);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos de los ingredientes");
        }
		return $result;
	}
}