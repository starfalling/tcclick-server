<?php

class OsVersion{
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
		$sql = "select * from {os_versions}";
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
		$sql = "insert into {os_versions} (version) values (:version)";
		if(TCClick::app()->db->execute($sql, array(":version"=>$version))){
			self::$all_versions[$version] = TCClick::app()->db->lastInsertId();
		}
	}
	
	public static function idFor($version){
		if(preg_match('|^[0-9\\.]+|', $version, $matches)){
			// 把 1.5.1.13-RT-20120509.203629 这种奇怪格式的版本号信息中的版本号前面部分提取出来
			$version = $matches[0];
		}
		if(empty($version)) return null;
		$all_versions = self::all();
		if(!$all_versions[$version]){
			self::add($version);
			$all_versions = self::all();
		}
		return $all_versions[$version];
	}
	/**
	 * @return string $osVersion
	 */
	public static function nameof($id){
	  foreach (self::all() as $name=>$version_id){
	    if($version_id == $id) return  $name;
	  }
	}
}
