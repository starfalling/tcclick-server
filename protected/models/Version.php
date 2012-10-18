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
	
	private static function reload($refreshCache=false){
		if(!$refreshCache){
			self::$all_versions = TCClick::app()->cache->get('tcclick_all_versions');
		}else{
			self::$all_versions = false;
		}
		if(self::$all_versions === false){
			self::$all_versions = array();
			$sql = "select * from {versions}";
			$stmt = TCClick::app()->db->query($sql);
			while(true){
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				if(!$row) break;
				self::$all_versions[$row['version']] = $row['id'];
			}
			TCClick::app()->cache->set('tcclick_all_versions', self::$all_versions);
		}
		return self::$all_versions;
	}
	
	/**
	 * add a version to database by name
	 * @param string $version
	 */
	public static function add($version){
		$sql = "insert ignore into {versions} (version) values (:version)";
		TCClick::app()->db->execute($sql, array(":version"=>$version));
		self::reload(true);
	}

	/**
	 * query unique id of the version in database, create one if not exist
	 * @param string $version
	 */
	public static function idFor($version){
		$all_versions = self::all();
		if(!$all_versions[$version]){
			self::add($version);
			$all_versions = self::all();
		}
		return $all_versions[$version];
	}
	
	/**
	 * query version name by version id
	 * @param integer $id
	 * @return string
	 */
	public static function nameOf($id){
		foreach(self::all() as $name=>$version_id){
			if($version_id == $id) return $name;
		}
	}
}

