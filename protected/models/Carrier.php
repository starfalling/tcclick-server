<?php

class Carrier{
	private static $all_carrier = null;
	
	/**
	 * get all the carrier from databases
	 * @return array associated array, key is the carrier name and value is the carrier id
	 */
	public static function all(){
		if(self::$all_carrier === null){
			self::reload();
		}
		return self::$all_carrier;
	}
	
	private static function reload(){
		self::$all_carrier = array();
		$sql = "select * from {carrier}";
		$stmt = TCClick::app()->db->query($sql);
		while(true){
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if(!$row) break;
			self::$all_carrier[$row['carrier']] = $row['id'];
		}
	}
	/**
	 * add a carrier to database by name
	 * @param string $carrier
	 */
	public static function add($carrier){
		$sql = "insert ignore into {carrier} (carrier) values (:carrier)";
		if(TCClick::app()->db->execute($sql, array(":carrier"=>$carrier))){
			self::$all_carrier[$carrier] = TCClick::app()->db->lastInsertId();
		}
	}
	
	/**
	 * @param string $carrier
	 */
	public static function idFor($carrier){
		if(!$carrier) return null;
		$carrier = trim($carrier);
		static $other_names = array(
				"China Mobile Communication Corp."=>"中国移动",
				"CHINA MOBILE" => "中国移动",
				"China Mobile" => "中国移动",
				"中国移动3G"     => "中国移动",
				"China Unicom" => "中国联通",
				"CHN-CUGSM"    => "中国联通",
				"CHN-UNICOM"   => "中国联通",
				"CU-GSM"       => "中国联通",
				"China Telecom"=> "中国电信",
				"46003"        => "中国电信",
				"460003"       => "中国电信",
				"Far EasTone"  => "遠傳電信",
				"远传电信"       => "遠傳電信",
		);
		if ($other_names[$carrier]) $carrier = $other_names[$carrier];
		$all_carrier = self::all();
		if(!$all_carrier[$carrier]){
			self::add($carrier);
			$all_carrier = self::all();
		}
		return $all_carrier[$carrier];
	}
	
	/**
	 * @return string $name
	 */
	public static function nameof($id){
	  foreach (self::all() as $name=>$carrier_id){
	    if($carrier_id == $id) return $name;
	  }
	}
}

