<?php

/**
 * listener to deal with version updates statistics
 * @author york
 */
class AnalyzeListenerVersionUpdateDevice implements IAnalyzeListener{
	
	public function execute($analyze){
		if(!$analyze->device->id) return;
		if(!$analyze->device->is_update) return;
		
		$date = date("Y-m-d", $analyze->server_timestamp);
		$hour = date("H", $analyze->server_timestamp);

		// hourly update device counter
		$sql = "insert into {counter_hourly_update} (`date`, `hour`, `channel_id`, `count`)
		values (:date, :hour, :channel_id, 1)
		on duplicate key update `count`=`count`+1";
		$params = array(":date"=>$date, ":hour"=>$hour, ":channel_id"=>$analyze->device->channel_id);
		TCClick::app()->db->execute($sql, $params);
		$params[":channel_id"] = Channel::CHANNEL_ID_ALL;
		TCClick::app()->db->execute($sql, $params);
		
		// daily update device counter
		$sql = "insert into {counter_daily_update} (`date`, `channel_id`, `count`)
		values (:date, :channel_id, 1)
		on duplicate key update `count`=`count`+1";
		$params = array(":date"=>$date, ":channel_id"=>$analyze->device->channel_id);
		TCClick::app()->db->execute($sql, $params);
		$params[":channel_id"] = Channel::CHANNEL_ID_ALL;
		TCClick::app()->db->execute($sql, $params);
		
		// daily counter
		$sql = "insert into {counter_daily} (`date`, `update_devices_count`)
		values (:date, 1)
		on duplicate key update `update_devices_count`=`update_devices_count`+1";
		TCClick::app()->db->execute($sql, array(":date"=>$date));
		
		// daily update device counter with version
		$sql = "insert into {counter_daily_update_with_version} (`date`, `version_id`, `count`)
		values (:date, :version_id, 1)
		on duplicate key update `count`=`count`+1";
		$params = array(":date"=>$date, ":version_id"=>$analyze->device->version_id);
		TCClick::app()->db->execute($sql, $params);
	}
}

