<?php

class TCClickCounter{
	
	public static function calculateNewDevicesCountOn($date){
		$sql = "select count(*) from {devices}
		where created_at>='{$date}' and created_at<='{$date} 23:59:59'";
		$stmt = TCClick::app()->db->query($sql, array(":date"=>$date));
		$count = $stmt->fetchColumn(0);
		return $count ? $count : 0;
	}
	
	public static function calculateActiveDevicesCountOn($date){
		$date_for_tablename = str_replace("-", "_", $date);
		$tablename = "daily_active_devices_{$date_for_tablename}";
		$sql = "select count(*) from {{$tablename}}";
		$stmt = TCClick::app()->db->query($sql);
		$count = $stmt->fetchColumn(0);
		return $count ? $count : 0;
	}
	
	public static function calculateUpdateDevicesCountOn($date, $channel_id=0){
		$sql = "select `count` from {counter_daily_update} 
		where date=:date and channel_id={$channel_id}";
		$stmt = TCClick::app()->db->query($sql, array(":date"=>$date));
		$count = $stmt->fetchColumn(0);
		return $count ? $count : 0;
	}
	
	public static function calculateOpenTimesOn($date){
		$date_for_tablename = str_replace("-", "_", $date);
		$tablename = "daily_active_devices_{$date_for_tablename}";
		$sql = "select sum(open_times) from {{$tablename}}";
		$stmt = TCClick::app()->db->query($sql);
		$count = $stmt->fetchColumn(0);
		return $count ? $count : 0;
	}
	
	public static function calculateSecondsSpentPerOpen($date){
		$date_for_tablename = str_replace("-", "_", $date);
		$tablename = "daily_active_devices_{$date_for_tablename}";
		$sql = "select sum(open_times_with_seconds_spent), sum(seconds_spent) from {{$tablename}}";
		$stmt = TCClick::app()->db->query($sql);
		$row = $stmt->fetch(PDO::FETCH_BOTH);
		if($row && $row[1]){
			return $row[1] / $row[0];
		}
		return 0;
	}
}

