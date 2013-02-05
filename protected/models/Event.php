<?php

class Event{
	public $id;
	public $name_id;
	public $alias_id;
	
	public static function all(){
		$result = array();
		$sql = "select * from {events}";
		$stmt = TCClick::app()->db->query($sql);
		while(($row=$stmt->fetch(PDO::FETCH_ASSOC)) != null){
			$instance = new self;
			$instance->initWithDbRow($row);
			$result[] = $instance;
		}
		return $result;
	}
	
	public static function idFor($name_id){
		$sql = "insert into {events} (name_id) values (:name_id)
		on duplicate key update id=last_insert_id(id)";
		TCClick::app()->db->execute($sql, array(':name_id'=>$name_id));
		return TCClick::app()->db->lastInsertId();
	}
	
	public static function loadById($id){
		$sql = "select * from {events} where id=:id";
		$row = TCClick::app()->db->query($sql, array(':id'=>$id))->fetch(PDO::FETCH_ASSOC);
		if($row){
			$instance = new self;
			$instance->initWithDbRow($row);
			return $instance;
		}
	}
	

	private function initWithDbRow($row){
		$this->id = $row['id'];
		$this->name_id = $row['name_id'];
		$this->alias_id = $row['alias_id'];
	}
}

