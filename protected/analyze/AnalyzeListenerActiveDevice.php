<?php

/**
 * listener to deal with active devices statistics
 * @author york
 */
class AnalyzeListenerActiveDevice implements IAnalyzeListener{
	public function execute($analyze){
		if(!$analyze->device->id) return;
		$device_id = $analyze->device->id;
		$channel_id = $analyze->device->channel_id;
		$hour_accessed = date("Y-m-d.H", $analyze->server_timestamp);
		$date_accessed = date("Y-m-d", $analyze->server_timestamp);
		$active_hours = array(); // 记录这次数据上传所涉及到的这台设备有哪些个小时是处于活跃状态
		$active_dates = array(); // 记录这次数据上传所涉及到的这台设备有哪些个天是处于活跃状态
		$active_weeks = array(); // 记录这次数据上传所涉及到的这台设备有哪些个周是处于活跃状态
		$active_months = array(); // 记录这次数据上传所涉及到的这台设备有哪些个月是处于活跃状态
		$active_hours[$hour_accessed] = true; // 客户端上传数据的时候肯定是活跃的
		$active_dates[$date_accessed] = true;
		$activities = $analyze->json->data->activities;
		if($activities){
			foreach($activities as $activity){
				// 确定这台设备哪些个小时是活跃的
				for($time=$activity->start_at; $time<=$activity->end_at; $time+=3600){
					$active_hours[date('Y-m-d.H', $time)] = true;
					$active_dates[date('Y-m-d', $time)] = true;
				}
			}
		}
		
		// 小时活跃
		foreach($active_hours as $active_hour=>$temp){
			$date = substr($active_hour, 0, 10);
			$hour = intval(substr($active_hour, 11));
			
			// 如果超过了小时活跃设备的最长记录时间，也就是上传上来的这个数据是太久以前的活跃数据，那么丢弃掉这个数据
			$time = strtotime($date.' '.$hour.':00:00');
			if(time()-$time > TCCLICK_HOURLY_ACTIVE_DEVICE_RECORD_TIME) continue;
			
			$date_for_tablename = str_replace("-", "_", $date);
			$tablename = "hourly_active_devices_{$date_for_tablename}";
			$sql = "insert ignore into {{$tablename}} (hour, device_id) values ({$hour}, {$device_id})";
			$result = TCClick::app()->db->execute($sql, null, $errorInfo);
			if($errorInfo && $errorInfo[0] = "42S02"){ // table not exists
				$sql_create = "create table {{$tablename}} (
					hour tinyint not null,
					device_id integer unsigned not null,
					primary key (hour, device_id)
				)";
				TCClick::app()->db->execute($sql_create);
				$result = TCClick::app()->db->execute($sql);
			}
			
			if($result === 1){ // 如果插入到数据库了，修改相关统计计数
				$sql = "insert into {counter_hourly_active} (date, hour, channel_id, `count`)
				values ('{$date}', {$hour}, :channel_id, 1)
				on duplicate key update `count`=`count`+1";
				TCClick::app()->db->execute($sql, array(":channel_id"=>Channel::CHANNEL_ID_ALL));
				TCClick::app()->db->execute($sql, array(":channel_id"=>$channel_id));
			}
		}
		
		// 日活跃
		foreach($active_dates as $date=>$temp){
			// 如果超过了小时活跃设备的最长记录时间，也就是上传上来的这个数据是太久以前的活跃数据，那么丢弃掉这个数据
			if(time()-strtotime($date) > TCCLICK_DAILY_ACTIVE_DEVICE_RECORD_TIME) continue;
			
			$date_for_tablename = str_replace("-", "_", $date);
			$tablename = "daily_active_devices_{$date_for_tablename}";
			$sql = "insert ignore into {{$tablename}} (device_id) values ({$device_id})";
			$result = TCClick::app()->db->execute($sql, null, $errorInfo);
			if($errorInfo && $errorInfo[0] = "42S02"){ // table not exists
				  $sql_create = "create table {{$tablename}} (
						device_id integer unsigned not null primary key,
						open_times tinyint unsigned not null default 0,
						open_times_with_seconds_spent tinyint unsigned not null default 0,
						seconds_spent smallint unsigned not null default 0
					)";
				  TCClick::app()->db->execute($sql_create);
				  $result = TCClick::app()->db->execute($sql);
			}
			if($result === 1){
				$sql = "insert into {counter_daily_active} (`date`, channel_id, `count`)
				values ('{$date}', :channel_id, 1)
				on duplicate key update `count`=`count`+1";
				TCClick::app()->db->execute($sql, array(":channel_id"=>Channel::CHANNEL_ID_ALL));
				TCClick::app()->db->execute($sql, array(":channel_id"=>$channel_id));
				
				// 版本活跃
				if($analyze->device->version_id){
					$sql = "insert into {counter_daily_active_version} (`date`, version_id, count)
					values ('{$date}', {$analyze->device->version_id}, 1)
					on duplicate key update `count`=`count`+1";
					TCClick::app()->db->execute($sql);
				}
				
				// 分辨率活跃
				if($analyze->device->resolution_id){
					$sql = "insert into {counter_daily_active_resolution} (`date`, resolution_id, count)
					values ('{$date}', {$analyze->device->resolution_id}, 1)
					on duplicate key update `count`=`count`+1";
					TCClick::app()->db->execute($sql);
				}
				
				// 操作系统版本活跃
				if($analyze->device->os_version_id){
					$sql = "insert into {counter_daily_active_os_version} (`date`, version_id, count)
					values ('{$date}', {$analyze->device->os_version_id}, 1)
					on duplicate key update `count`=`count`+1";
					TCClick::app()->db->execute($sql);
				}
				
				// 网络条件活跃
				if($analyze->device->network_id){
					$sql = "insert into {counter_daily_active_network} (`date`, network_id, count)
					values ('{$date}', {$analyze->device->network_id}, 1)
					on duplicate key update `count`=`count`+1";
					TCClick::app()->db->execute($sql);
				}
				
				// 设备型号活跃
				if($analyze->device->model_id){
					$sql = "insert into {counter_daily_active_model} (`date`, model_id, count)
					values ('{$date}', {$analyze->device->model_id}, 1)
					on duplicate key update `count`=`count`+1";
					TCClick::app()->db->execute($sql);
				}
				
				// 运营商活跃
				if($analyze->device->carrier_id){
					$sql = "insert into {counter_daily_active_carrier} (`date`, carrier_id, count)
					values ('{$date}', {$analyze->device->carrier_id}, 1)
					on duplicate key update `count`=`count`+1";
					TCClick::app()->db->execute($sql);
				}
				
				// 地理位置地区
				if($analyze->area_id){
					$sql = "insert into {counter_daily_active_area} (`date`, area_id, count)
					values ('{$date}', {$analyze->area_id}, 1)
					on duplicate key update `count`=`count`+1";
					TCClick::app()->db->execute($sql);
				}
				
				// 标记这一天所在的自然周、自然月用户是活跃的
				$week_start_date = $this->weekStartDateOf($date);
				$active_weeks[$week_start_date] = true;
				$active_months[substr($date, 0, 7)] = true;
			}
		}
		
		
		// 记录自然周活跃ID列表
		foreach($active_weeks as $date=>$temp){
			$date_for_tablename = str_replace("-", "_", $date);
			$tablename = "weekly_active_devices_{$date_for_tablename}";
			$sql = "insert ignore into {{$tablename}} (device_id) values ({$device_id})";
			$result = TCClick::app()->db->execute($sql, null, $errorInfo);
			if($errorInfo && $errorInfo[0] = "42S02"){ // table not exists
				$sql_create = "create table {{$tablename}} (
					device_id integer unsigned not null,
					primary key (device_id)
				)";
				TCClick::app()->db->execute($sql_create);
				$result = TCClick::app()->db->execute($sql);
			}
			if ($result===1){ // 增加周活跃计数器
				$sql = "insert into {counter_weekly_active} (`date`, channel_id, `count`)
				values ('{$date}', :channel_id, 1)
				on duplicate key update `count`=`count`+1";
				TCClick::app()->db->execute($sql, array(":channel_id"=>Channel::CHANNEL_ID_ALL));
				TCClick::app()->db->execute($sql, array(":channel_id"=>$channel_id));
			}
		}
		
		// 记录自然月活跃ID列表
		foreach($active_months as $date=>$temp){
			$date_for_tablename = str_replace("-", "_", $date);
			$tablename = "monthly_active_devices_{$date_for_tablename}";
			$sql = "insert ignore into {{$tablename}} (device_id) values ({$device_id})";
			$result = TCClick::app()->db->execute($sql, null, $errorInfo);
			if($errorInfo && $errorInfo[0] = "42S02"){ // table not exists
				$sql_create = "create table {{$tablename}} (
					device_id integer unsigned not null,
					primary key (device_id)
				)";
				TCClick::app()->db->execute($sql_create);
				$result = TCClick::app()->db->execute($sql);
			}
			if ($result===1){ // 增加月活跃计数器
				$sql = "insert into {counter_monthly_active} (`date`, channel_id, `count`)
				values ('{$date}-01', :channel_id, 1)
				on duplicate key update `count`=`count`+1";
				TCClick::app()->db->execute($sql, array(":channel_id"=>Channel::CHANNEL_ID_ALL));
				TCClick::app()->db->execute($sql, array(":channel_id"=>$channel_id));
			}
		}
	}
	
	/**
	 * @param mixed $date unix timestamp or date string with format like 2012-07-05
	 */
	private function weekStartDateOf($date){
		$time = is_numeric($date) ? $date : strtotime($date);
		$dayOfWeek = intval(date("N", $time));
		return date("Y-m-d", $time-86400*($dayOfWeek-1));
	}
}

