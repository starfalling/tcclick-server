<?php


class Config{
	const KEY_CURRENT_DB_VERSION = 'KEY_CURRENT_DB_VERSION';
	const KEY_DEVICE_COUNTS_WITH_ANDROID_INFO_SITE_ID = 'KEY_DEVICE_COUNTS_WITH_ANDROID_INFO_SITE_ID';
	const KEY_DEVICE_COUNTS_WITH_ANDROID_INFO_CAMPAIGN_ID = 'KEY_DEVICE_COUNTS_WITH_ANDROID_INFO_CAMPAIGN_ID';

	public static function get($key, $default=null){
		$sql = "select value from {configs} where `key`=:key";
		$row = TCClick::app()->db->query($sql, array(':key'=>$key))->fetch(PDO::FETCH_ASSOC);
		if($row) return unserialize($row['value']);
		return $default;
	}
	
	public static function set($key, $value){
		$sql = "insert into {configs} (`key`, `value`) values
				(:key, :value) on duplicate key update `value`=values(`value`)";
		TCClick::app()->db->execute($sql, array(':key'=>$key, ':value'=>serialize($value)));
	}
}

