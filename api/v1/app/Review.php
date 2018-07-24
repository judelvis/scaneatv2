<?php

class Review extends Result {
	var $review;
	function setReview($s) {
		$this->review = $s;
	}
	function getReview() {
		return $this->review;
	}

	static function getReviewsRestaurant($db,$rest_id){
		return Review::getReviewsRestaurantPaged($db,$rest_id,-1,-1);
	}

	static function getReviewsRestaurantPaged($db,$rest_id,$start,$size){
		$result = new Review();
		$sql = "select v.id,
			v.title,
			v.comentario,
            p.nombre as plateName,
			v.presentacion,
			v.calidadprecio,
			v.sabortextura,
			v.usuario_id,
			u.nombre,
			v.createDate,
			v.date,
			v.dia_o_noche 
			from valoraciones v inner join usuarios u ON u.id = v.usuario_id 
			inner join platos p ON v.plato_id = p.id
			WHERE p.rest_id = ".$rest_id." 
			ORDER BY v.createDate";
			
		if ($start != -1 && $size != -1) {
			$limit = " LIMIT ".$start.",".$size;
		} else {
			$limit = "";
		}
		$sql .= $limit;
		$query = $db->prepare($sql);
		$query->execute();

		$reviews = array();
		while($row=$query->fetch()){
			$id = $row["id"];
			unset($row['id']);
			$reviews[$id] = $row;
			$reviews[$id]["images"] = getReviewsImage($plate_id, $id);
		}
		$result->review = $reviews;
		$result->setStatus(OK);
		$result->setMessage("");
		return $result;
	}

	static function getReviewsPlate($db,$plate_id){
		return Review::getReviewsPlatePaged($db,$plate_id,-1,-1);
	}

	static function getReviewsPlatePaged($db,$plate_id,$start,$size){
		$result = new Review();
		$sql = "select v.id,v.title, v.comentario,v.presentacion,v.calidadprecio,v.sabortextura,v.usuario_id,u.nombre,v.createDate,v.date,v.dia_o_noche 
from valoraciones v inner join usuarios u ON u.id = v.usuario_id 
WHERE v.plato_id=".$plate_id;

		if ($start != -1 && $size != -1) {
			$limit = " LIMIT ".$start.",".$size;
		} else {
			$limit = "";
		}
		$sql .= $limit;
		$query = $db->prepare($sql);
		$query->execute();
		$reviews = Review::parseReviewPlate($query,$plate_id);
		$result->review = $reviews;
		$result->setStatus(OK);
		$result->setMessage("");
		return $result;
	}

	static function writeReview($db,$plate_id,$title,$comment ,$presentation, $price_quality,
	$savour_texture, $user_id, $date , $day_night){
		$result = new Review();
		$sql = "INSERT INTO valoraciones (plato_id,title,comentario,presentacion,calidadprecio,
		sabortextura,usuario_id,createDate,date,dia_o_noche)
		VALUES (".$plate_id.",'".$title."','".$comment."',".$presentation.",".$price_quality.",".
		$savour_texture.",".$user_id.",now(),STR_TO_DATE('".$date."', '%d/%m/%Y'),".$day_night.")";
		$query = $db->prepare($sql);
		$query->execute();
		$id = $db->lastInsertId();
		$result->setStatus(OK);
		$result->setMessage($id);
		return $result;
	}

	static function parseReviewPlate($query,$plate_id) {
		$num_rows_userExist = $query->rowCount();
		$reviews = null;
		if($num_rows_userExist > 0) {
			$reviews = array();
			while($row=$query->fetch()){
				$id = $row["id"];
				unset($row['id']);
				$reviews[$id] = $row;
				$reviews[$id]["images"] = getReviewsImage($plate_id, $id);
			}
		}
		return $reviews;
	}
	
	static function getReviewStatsByPlateId($db, $plate_id) {
		$sql = "SELECT SUM(IF(avg < 1.5, 1, 0)) as '1', 
			SUM(IF(avg >= 1.5 and avg < 2.5, 1, 0)) as '2', 
			SUM(IF(avg >= 2.5 and avg < 3.5, 1, 0)) as '3', 
			SUM(IF(avg >= 3.5 and avg < 4.5, 1, 0)) as '4', 
			SUM(IF(avg >= 4.5, 1, 0)) as '5',
			COUNT(*) as count
		FROM (
			select ((v.presentacion + v.calidadprecio + v.sabortextura)/3) as avg 
			from valoraciones v inner join usuarios u ON u.id = v.usuario_id 
			WHERE v.plato_id=".$plate_id.") as reviewstats";
		$query = $db->prepare($sql);
		$query->execute();
		$num_rows_userExist = $query->rowCount();
		if($num_rows_userExist > 0) {
			while($row=$query->fetch(PDO::FETCH_ASSOC)){
				$stats = $row;
			}
		}
		return $stats;
	}

	static function getReviewStatsByRestaurantId($db, $rest_id) {
		$sql = "SELECT SUM(IF(avg < 1.5, 1, 0)) as '1', 
			SUM(IF(avg >= 1.5 and avg < 2.5, 1, 0)) as '2', 
			SUM(IF(avg >= 2.5 and avg < 3.5, 1, 0)) as '3', 
			SUM(IF(avg >= 3.5 and avg < 4.5, 1, 0)) as '4', 
			SUM(IF(avg >= 4.5, 1, 0)) as '5',
			COUNT(*) as count
		FROM (
			select ((v.presentacion + v.calidadprecio + v.sabortextura)/3) as avg
			from valoraciones v inner join usuarios u ON u.id = v.usuario_id 
			inner join platos p ON v.plato_id = p.id
			WHERE p.rest_id = ".$rest_id." 
			) as reviewstats";
		$query = $db->prepare($sql);
		$query->execute();
		$num_rows_userExist = $query->rowCount();
		if($num_rows_userExist > 0) {
			while($row=$query->fetch(PDO::FETCH_ASSOC)){
				$stats = $row;
			}
		}
		return $stats;
	}
}
?>