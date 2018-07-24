<?php

define('ERROR_LOADING_REST_SCHEDULE', 2050);
class RestSchedule extends Result{
	var $restSchedule;

	function setRestSchedule($s) {
		$this->restSchedule = $s;
	}
	
	function getRestSchedule() {
		return $this->restSchedule;
	}

    static function getRestScheduleByRestaurantId($db, $restId){
		$result = new RestSchedule();
        $sql = "SELECT * from horariorestaurantes
        where rest_id = ".$restId." order by day asc";
        $query = $db->prepare($sql);
        $query->execute();
        $num_rows_userExist = $query->rowCount();
        if($num_rows_userExist > 0) {
            $list = array();
            while($row=$query->fetch()){
                $item = array('opt1'=>$row["opt1"],
                'opt2'=>$row["opt2"],
                'opt3'=>$row["opt3"],
                'opt4'=>$row["opt4"]);
                $list[$row["day"]] = $item;
            }
            $result->setStatus(OK);
            $result->setMessage("");
            $result->setRestSchedule($list);
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay daots del horario del restaurante");
        }
		return $result;
	}
    
    static function updateRestaurant($db,$rest_id, $restSchedule) {
        $sql_delete = "delete from horariorestaurantes where rest_id = ".$rest_id;
        $query = $db->prepare($sql_delete);
        $query->execute();

        $values = "VALUES ";
        foreach ($restSchedule as $key => $value) {
            $opt1 = $value["opt1"];
            $opt2 = $value["opt2"];
            $opt3 = $value["opt3"];
            $opt4 = $value["opt4"];
            $values .= "(".$rest_id.",".$key.",'".$opt1."','".$opt2."','".$opt3."','".$opt4."'),";
        }
        if ($values != "VALUES ") {
            $values=rtrim($values,",");
            $sql_update = "INSERT INTO horariorestaurantes (rest_id, day, opt1, opt2, opt3, opt4) ".$values;
            error_log($sql_update);
            $query = $db->prepare($sql_update);
            $query->execute();
        }
    }
    
}