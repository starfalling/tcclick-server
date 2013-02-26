<?php

class Area{
	/**
	 * add a area to database by name
	 * @param string $area
	 */
	public static function add($area){
		$sql = "insert into {areas} (area) values (:area)";
		TCClick::app()->db->execute($sql, array(":area"=>$area));
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

		$sql = "select * from {areas} where area=:area";
		$stmt = TCClick::app()->db->query($sql, array(':area'=>$area));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if($row) return $row['id'];
		self::add($area);
		$stmt = TCClick::app()->db->query($sql, array(':area'=>$area));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if($row) return $row['id'];
	}
	
	/**
	 * @return string $area
	 */
	public static function nameof($id){
		if($id > 0 && $id < 35){
			$china_provinces = explode(",", ",中国,北京,上海,天津,重庆,安徽,福建,甘肃,广东,广西,贵州,"
					."海南,河北,河南,黑龙江,湖北,湖南,吉林,江苏,江西,辽宁,内蒙古,宁夏,青海,山东,山西,陕西,四川,西藏,新疆,"
					."云南,浙江,香港,澳门,台湾");
			return $china_provinces[$id];
		}else{
			$sql = "select area from {areas} where id=:id";
			$stmt = TCClick::app()->db->query($sql, array(':id'=>$id));
			return $stmt->fetchColumn(0);
		}
	}
}

