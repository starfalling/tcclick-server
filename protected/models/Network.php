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
	
	private static function reload(){
		self::$all_networks = array();
		$sql = "select * from {networks}";
		$stmt = TCClick::app()->db->query($sql);
		while(true){
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if(!$row) break;
			self::$all_networks[$row['network']] = $row['id'];
		}
	}
	/**
	 * add a network to database by name
	 * @param string $network
	 */
	public static function add($network){
		$sql = "insert into {networks} (network) values (:network)";
		if(TCClick::app()->db->execute($sql, array(":network"=>$network))){
			self::$all_networks[$network] = TCClick::app()->db->lastInsertId();
		}
	}
	
	/**
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
	 * @return string $name
	 */
	public static function nameof($id){
	  foreach (self::all() as $name=>$network_id){
	    if($network_id == $id) return $name;
	  }
	}
}

