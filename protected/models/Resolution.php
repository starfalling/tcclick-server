<?php

class Resolution{
	private static $all_resolutions = null;
	
	/**
	 * get all the resolutions from databases
	 * @return array associated array, key is the resolution name and value is the resolution id
	 */
	public static function all(){
		if(self::$all_resolutions === null){
			self::reload();
		}
		return self::$all_resolutions;
	}
	
	private static function reload(){
		self::$all_resolutions = array();
		$sql = "select * from {resolutions}";
		$stmt = TCClick::app()->db->query($sql);
		while(true){
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if(!$row) break;
			self::$all_resolutions[$row['resolution']] = $row['id'];
		}
	}
	/**
	 * add a resolution to database by name
	 * @param string $resolution
	 */
	public static function add($resolution){
		$sql = "insert into {resolutions} (resolution) values (:resolution)";
		if(TCClick::app()->db->execute($sql, array(":resolution"=>$resolution))){
			self::$all_resolutions[$resolution] = TCClick::app()->db->lastInsertId();
		}
	}
	
	/**
	 * @param string $resolution
	 */
	public static function idFor($resolution){
		$all_resolutions = self::all();
		if(!$all_resolutions[$resolution]){
			self::add($resolution);
			$all_resolutions = self::all();
		}
		return $all_resolutions[$resolution];
	}
	
	public static function nameof($id){
	  foreach (self::all() as $name=>$resolution_id){
	    if($id == $resolution_id) return $name;
	  }
	}
}

