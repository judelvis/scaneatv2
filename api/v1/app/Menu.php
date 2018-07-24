<?php

class Menu extends Result{
	var $menu;
	
	function setMenu($s) {
		$this->menu = $s;
	}
	
	function getMenu() {
		return $this->menu;
	}

    static function getMenuByRestaurantId($db, $rest_id) {
        $result = new Menu();

        $sql="select scr.id_seccion, sc.name, scr.orden from seccionescarta_restaurante scr
        inner join seccionescarta sc on scr.id_seccion = sc.id
        where scr.id_restaurante = ".$rest_id." 
        order by scr.orden ASC";
        $query = $db->prepare($sql);
        $query->execute();
        $num_rows_userExist = $query->rowCount();
        if($num_rows_userExist > 0) {
            $menu = array();
            while($row=$query->fetch()){
                $id = $row["id_seccion"];
			    unset($row['id_seccion']);
			    $menu[$id] = $row;
            }
            $result->setStatus(OK);
            $result->setMenu($menu);
            $result->setMessage("");
        } else {
            $result->setStatus(VOID);
            $result->setMessage("No hay datos del menu");
        }

        return $result;
    }
}