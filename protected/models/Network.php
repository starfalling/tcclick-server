<?php

class Network{
	private static $all_networks = null;
	
	/**
	 * get all the networks from databases
	 * @return array associated array, key is the network name and value is the network id
	 */
	public static function all(){
		if(self::$all_networks === null){
			self::reload();
		}
		return self::$all_networks;
	}
	
	private static function reload($refreshCache=false){
		if(!$refreshCache){
			self::$all_networks = TCClick::app()->cache->get('tcclick_all_networks');
		}else{
			self::$all_networks = false;
		}
		if(self::$all_networks === false){
			self::$all_networks = array();
			$sql = "select * from {networks}";
			$stmt = TCClick::app()->db->query($sql);
			while(true){
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				if(!$row) break;
				self::$all_networks[$row['network']] = $row['id'];
			}
			TCClick::app()->cache->set('tcclick_all_networks', self::$all_networks);
		}
		return self::$all_networks;
	}
	
	/**
	 * add a network to database by name
	 * @param string $network
	 */
	public static function add($network){
		$sql = "insert ignore into {networks} (network) values (:network)";
		TCClick::app()->db->execute($sql, array(":network"=>$network));
		self::reload(true);
	}
	
	/**
	 * query unique id of the network in database, create one if not exist 
	 * @param string $network
	 */
	public static function idFor($network){
		$all_networks = self::all();
		if(!$all_networks[$network]){
			self::add($network);
			$all_networks = self::all();
		}
		return $all_networks[$network];
	}
	
	/**
	 * query network name by chanel id
	 * @param integer $id
	 * @return string
	 */
	public static function nameOf($id){
		foreach(self::all() as $name=>$network_id){
			if($network_id == $id) return $name;
		}
	}
}

