<?php

/**
 * listener to deal with new devices statistics
 * @author york
 */
class AnalyzeListenerNewDevice implements IAnalyzeListener{
	
	public function execute($analyze){
		if(!$analyze->device->id) return;
		if(!$analyze->device->is_new) return;
		
		$date = date("Y-m-d", $analyze->server_timestamp);
		$hour = date("H", $analyze->server_timestamp);
		
		// daily counter TODO
		$sql = "insert into {counter_daily} (date, new_devices_count, all_devices_count)
		values (:date, 1, 1) on duplicate key update 
		new_devices_count=new_devices_count+1,
		all_devices_count=all_devices_count+1";
		TCClick::app()->db->execute($sql, array(":date"=>$date));
		
		// hourly new device counter
		$sql = "insert into {counter_hourly_new} (`date`, `hour`, `channel_id`, `count`)
		values (:date, :hour, :channel_id, 1) 
		on duplicate key update `count`=`count`+1";
		$params = array(":date"=>$date, ":hour"=>$hour, ":channel_id"=>$analyze->device->channel_id);
		TCClick::app()->db->execute($sql, $params);
		$params[":channel_id"] = Channel::CHANNEL_ID_ALL;
		TCClick::app()->db->execute($sql, $params);
		
		// daily new device counter
		$sql = "insert into {counter_daily_new} (`date`, `channel_id`, `count`)
		values (:date, :channel_id, 1)
		on duplicate key update `count`=`count`+1";
		$params = array(":date"=>$date, ":channel_id"=>$analyze->device->channel_id);
		TCClick::app()->db->execute($sql, $params);
		$params[":channel_id"] = Channel::CHANNEL_ID_ALL;
		TCClick::app()->db->execute($sql, $params);
		
		// daily new device counter with version
		$sql = "insert into {counter_daily_new_version} (`date`, `version_id`, `count`)
		values (:date, :version_id, 1)
		on duplicate key update `count`=`count`+1";
		$params = array(":date"=>$date, ":version_id"=>$analyze->device->version_id);
		TCClick::app()->db->execute($sql, $params);
		
		if($analyze->device->model_id){
			// daily new device counter with brand and model
			$sql = "insert into {counter_daily_new_model} (`date`, `model_id`, `count`)
			values (:date, :model_id, 1)
			on duplicate key update `count`=`count`+1";
			$params = array(":date"=>$date, ":model_id"=>$analyze->device->model_id);
			TCClick::app()->db->execute($sql, $params);
		}
		
		// daily new device counter with os version
		$sql = "insert into {counter_daily_new_os_version} (`date`, `version_id`, `count`)
		values (:date, :version_id, 1)
		on duplicate key update `count`=`count`+1";
		$params = array(":date"=>$date, ":version_id"=>$analyze->device->os_version_id);
		TCClick::app()->db->execute($sql, $params);
		
		// daily new device counter with resolution
		$sql = "insert into {counter_daily_new_resolution} (`date`, `resolution_id`, `count`)
		values (:date, :resolution_id, 1)
		on duplicate key update `count`=`count`+1";
		$params = array(":date"=>$date, ":resolution_id"=>$analyze->device->resolution_id);
		TCClick::app()->db->execute($sql, $params);
		
		// daily new device counter with carrier
		if($analyze->device->carrier){
			$sql = "insert into {counter_daily_new_carrier} (`date`, `carrier_id`, `count`)
			values (:date, :carrier_id, 1)
			on duplicate key update `count`=`count`+1";
			$params = array(":date"=>$date, ":carrier_id"=>$analyze->device->carrier_id);
			TCClick::app()->db->execute($sql, $params);
		}
		
		// daily new device counter with area
		if($analyze->area_id){
			$sql = "insert into {counter_daily_new_area} (`date`, `area_id`, `count`)
			values (:date, :area_id, 1)
			on duplicate key update `count`=`count`+1";
			$params = array(":date"=>$date, ":area_id"=>$analyze->area_id);
			TCClick::app()->db->execute($sql, $params);
		}
	}
}

