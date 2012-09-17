<?php

class Channel{
	private static $all_channels = null;
	const CHANNEL_ID_ALL = 0;
	
	/**
	 * get all the channels from databases
	 * @return array associated array, key is the channel name and value is the channel id
	 */
	public static function all(){
		if(self::$all_channels === null){
			self::reload();
		}
		return self::$all_channels;
	}
	
	private static function reload(){
		self::$all_channels = array();
		$sql = "select * from {channels}";
		$stmt = TCClick::app()->db->query($sql);
		while(true){
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if(!$row) break;
			self::$all_channels[$row['channel']] = $row['id'];
		}
	}
	/**
	 * add a channel to database by name
	 * @param string $channel
	 */
	public static function add($channel){
		$sql = "insert ignore into {channels} (channel) values (:channel)";
		TCClick::app()->db->execute($sql, array(":channel"=>$channel));
		self::reload();
	}
	
	/**
	 * @param string $channel
	 */
	public static function idFor($channel){
		$all_channels = self::all();
		if(!$all_channels[$channel]){
			self::add($channel);
			$all_channels = self::all();
		}
		return $all_channels[$channel];
	}
	
	public static function nameOf($id){
		foreach(self::all() as $name=>$channel_id){
			if($channel_id == $id) return $name;
		}
	}
}

