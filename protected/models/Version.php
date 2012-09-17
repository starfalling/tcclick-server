<?php

class Version{
	private static $all_versions = null;
	
	/**
	 * get all the versions from databases
	 * @return array associated array, key is the version name and value is the version id
	 */
	public static function all(){
		if(self::$all_versions === null){
			self::reload();
		}
		return self::$all_versions;
	}
	
	private static function reload(){
		self::$all_versions = array();
		$sql = "select * from {versions}";
		$stmt = TCClick::app()->db->query($sql);
		while(true){
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if(!$row) break;
			self::$all_versions[$row['version']] = $row['id'];
		}
	}
	/**
	 * add a channel to database by name
	 * @param string $version
	 */
	public static function add($version){
		$sql = "insert into {versions} (version) values (:version)";
		if(TCClick::app()->db->execute($sql, array(":version"=>$version))){
			self::$all_versions[$version] = TCClick::app()->db->lastInsertId();
		}
	}
	
	public static function idFor($version){
		$all_versions = self::all();
		if(!$all_versions[$version]){
			self::add($version);
			$all_versions = self::all();
		}
		return $all_versions[$version];
	}
	
	public static function nameOf($id){
		foreach(self::all() as $name=>$version_id){
			if($version_id == $id) return $name;
		}
	}
}

