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
		$resolution_changed = preg_replace('|([0-9]+)x([0-9]+)|', '$2x$1', $resolution);
		
		// try to load cached resolution id from memcache
		static $cached_resolutions = null;
		if(!$cached_resolutions){
			$cache_key = "tcclick_cached_device_resolutions";
			$cached_resolutions = TCClick::app()->cache->get($cache_key);
		}
		if($cached_resolutions){
			if($cached_resolutions[$resolution]){
				return $cached_resolutions[$resolution];
			}elseif($cached_resolutions[$resolution_changed]){
				return $cached_resolutions[$resolution_changed];
			}
		}
		
		$all_resolutions = self::all();
		if ($all_resolutions[$resolution]){
			return $all_resolutions[$resolution];
		}elseif ($all_resolutions[$resolution_changed]){
			return $all_resolutions[$resolution_changed];
		}else{
			self::add($resolution);
			$all_resolutions = self::all();
			return $all_resolutions[$resolution];
		}
	}
	
	public static function nameof($id){
	  foreach (self::all() as $name=>$resolution_id){
	    if($id == $resolution_id) return $name;
	  }
	}
}

