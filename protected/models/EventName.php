<?php

class EventName{
	public static function idFor($name){
		$sql = "insert into {event_names} (name) values (:name) 
		on duplicate key update id=last_insert_id(id)";
		TCClick::app()->db->execute($sql, array(':name'=>$name));
		return TCClick::app()->db->lastInsertId();
	}
	
	public static function nameof($id){
		$sql = "select * from {event_names} where id=:id";
		$row = TCClick::app()->db->query($sql, array(':id'=>$id))->fetch(PDO::FETCH_ASSOC);
		if($row) return $row['name'];
	}
}

