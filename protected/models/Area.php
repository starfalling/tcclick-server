<?php

class Area{
	private static $all_areas = null;
	
	/**
	 * get all the areas from databases
	 * @return array associated array, key is the area name and value is the area id
	 */
	public static function all(){
		if(self::$all_areas === null){
			self::reload();
		}
		return self::$all_areas;
	}
	
	private static function reload(){
		self::$all_areas = array();
		$sql = "select * from {areas}";
		$stmt = TCClick::app()->db->query($sql);
		while(true){
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if(!$row) break;
			self::$all_areas[$row['area']] = $row['id'];
		}
	}
	/**
	 * add a area to database by name
	 * @param string $area
	 */
	public static function add($area){
		$sql = "insert into {areas} (area) values (:area)";
		TCClick::app()->db->execute($sql, array(":area"=>$area));
		self::reload();
	}
	
	/**
	 * @param string $area
	 */
	public static function idFor($area){
		static $china_province_ids = null;
		if(!$china_province_ids){
			$china_province_ids = array_flip(explode(",", ",中国,北京,上海,天津,重庆,安徽,福建,甘肃,广东,广西,贵州,"
					."海南,河北,河南,黑龙江,湖北,湖南,吉林,江苏,江西,辽宁,内蒙古,宁夏,青海,山东,山西,陕西,四川,西藏,新疆,"
					."云南,浙江,香港,澳门,台湾"));
		}
		if($china_province_ids[$area]) return $china_province_ids[$area];
		
		$all_areas = self::all();
		if(!$all_areas[$area]){
			self::add($area);
			$all_areas = self::all();
		}
		return $all_areas[$area];
	}
	
	/**
	 * @return string $area
	 */
	public static function nameof($id){
	  foreach (self::all() as $name=>$area_id){
	    if($area_id == $id) return  $name;
	  }
	}
}

