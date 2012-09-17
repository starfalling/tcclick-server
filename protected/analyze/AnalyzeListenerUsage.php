<?php

/**
 * listener to deal with app usage statistics, just like open times and seconds spent
 * @author york
 */
class AnalyzeListenerUsage implements IAnalyzeListener{
	
	public function execute($analyze){
		if(!$analyze->device->id) return;
		$activities = $analyze->json->data->activities;
		$hour_accessed = date("Y-m-d.H", $analyze->server_timestamp);
		$date_accessed = date("Y-m-d", $analyze->server_timestamp);
		$device_id = $analyze->device->id;
		
		$open_times_dates = array();
		// 上传数据的这次要算一次启动，但是这次启动的使用时长需要下一次上传数据时候才能知道并进行计算
		$open_times_dates[$date_accessed] = 1;
		$open_times_hours[$hour_accessed] = 1;
		$open_times_with_seconds_spent_dates = array();
		$seconds_spent_dates = array();
		
		if(!empty($activities)){
			$prev_activity = $activities[0];
			$this_open_start_at = 0; // 当前进行计算的这次启动的启动时间
			$last_activity_index = count($activities) - 1;
			
			// 下面foeach循环会把收到的activity记录进行计算，得到响应的小时、天的应用启动次数之后记录临时变量中然后再一并存入数据库
			foreach($activities as $i=>$activity){
				if(!$this_open_start_at) $this_open_start_at = $activity->start_at;
				if($activity->start_at - $prev_activity->end_at > 10){// 两次activity之间活动时间相隔超过10秒，算作一次新的启动
					$start_date = date("Y-m-d", $this_open_start_at);
					$this_open_seconds_spent = $prev_activity->end_at - $this_open_start_at; // 当前进行计算的这次启动的使用时长
// 					echo "this open: start_at=", date("Y-m-d H:i:s", $this_open_start_at);
// 					echo "  end_at=", date("Y-m-d H:i:s", $prev_activity->end_at);
// 					echo "  seconds_spent=", $this_open_seconds_spent, "\n";
					
					// 进行单次使用时长区间的计数统计
					$seconds_spent_id = TCClick::secondsSpentIdFor($this_open_seconds_spent);
					$sql = "insert into {counter_daily_seconds_spent_per_open} (`date`, seconds_spent_id, count)
					values ('{$start_date}', {$seconds_spent_id}, 1)
					on duplicate key update `count`=`count`+1";
					TCClick::app()->db->execute($sql);
					
					// open_times_with_seconds_spent, seconds spent
					if($seconds_spent_dates[$start_date]){
						$seconds_spent_dates[$start_date] += $this_open_seconds_spent;
						$open_times_with_seconds_spent_dates[$start_date] += 1;
					}else{
						$seconds_spent_dates[$start_date] = $this_open_seconds_spent;
						$open_times_with_seconds_spent_dates[$start_date] = 1;
					}
					
					if($this_open_start_at == $activities[0]->start_at){
						// 这一次的启动次数已经在上一次用户上传数据的时候计入了，忽略
					}else{
						// 小时启动次数
						$hour = date("Y-m-d.H", $this_open_start_at);
						if($open_times_hours[$hour]) $open_times_hours[$hour] += 1;
						else $open_times_hours[$hour] = 1;
						
						if($open_times_dates[$start_date]) $open_times_dates[$start_date] += 1;
						else $open_times_dates[$start_date] = 1;
					}
					
					$this_open_start_at = $activity->start_at;
				}
				
				
				
				if ($i == $last_activity_index){ // 最后一个activity，结清当前这次启动数据
					$start_date = date("Y-m-d", $this_open_start_at);
					$this_open_seconds_spent = $activity->end_at - $this_open_start_at; // 当前进行计算的这次启动的使用时长
// 					echo "this open: start_at=", date("Y-m-d H:i:s", $this_open_start_at);
// 					echo "  end_at=", date("Y-m-d H:i:s", $activity->end_at);
// 					echo "  seconds_spent=", $this_open_seconds_spent, "\n";
						
					// 进行单次使用时长区间的计数统计
					$seconds_spent_id = TCClick::secondsSpentIdFor($this_open_seconds_spent);
					$sql = "insert into {counter_daily_seconds_spent_per_open} (`date`, seconds_spent_id, count)
					values ('{$start_date}', {$seconds_spent_id}, 1)
					on duplicate key update `count`=`count`+1";
					TCClick::app()->db->execute($sql);
						
					// open_times_with_seconds_spent, seconds spent
					if($seconds_spent_dates[$start_date]){
						$seconds_spent_dates[$start_date] += $this_open_seconds_spent;
						$open_times_with_seconds_spent_dates[$start_date] += 1;
					}else{
						$seconds_spent_dates[$start_date] = $this_open_seconds_spent;
						$open_times_with_seconds_spent_dates[$start_date] = 1;
					}
						
					if($this_open_start_at == $activities[0]->start_at){
						// 这一次的启动次数已经在上一次用户上传数据的时候计入了，忽略
					}else{
						// 小时启动次数
						$hour = date("Y-m-d.H", $this_open_start_at);
						if($open_times_hours[$hour]) $open_times_hours[$hour] += 1;
						else $open_times_hours[$hour] = 1;
					
						if($open_times_dates[$start_date]) $open_times_dates[$start_date] += 1;
						else $open_times_dates[$start_date] = 1;
					}
				}
				
				$prev_activity = $activity;
			}
		}
		
		
		// 把小时启动次数存入数据库
		foreach($open_times_hours as $date_hour=>$times){
			list($date, $hour) = explode(".", $date_hour);
			$sql = "insert into {counter_hourly_open_times} (`date`, hour, `count`)
			values ('{$date}', $hour, $times) on duplicate key 
			update `count`=`count`+{$times}";
			TCClick::app()->db->execute($sql);
		}
		
		// 把启动次数数据存入数据库
		foreach($open_times_dates as $date=>$times){
			$date_for_tablename = str_replace("-", "_", $date);
			$tablename = "daily_active_devices_{$date_for_tablename}";
			if(isset($seconds_spent_dates[$date])){ // 有使用时长数据
				$sql = "update {{$tablename}} set open_times=open_times+{$times},
				open_times_with_seconds_spent=open_times_with_seconds_spent+{$open_times_with_seconds_spent_dates[$date]},
				seconds_spent=seconds_spent+{$seconds_spent_dates[$date]}
				where device_id={$device_id}";
				TCClick::app()->db->execute($sql);
				unset($seconds_spent_dates[$date]);
				unset($open_times_with_seconds_spent_dates[$date]);
			}else{
				$sql = "update {{$tablename}} set open_times=open_times+{$times}
				where device_id={$device_id}";
				TCClick::app()->db->execute($sql);
			}
		}
		foreach($open_times_with_seconds_spent_dates as $date=>$times){
			$sql = "update {{$tablename}} set
			open_times_with_seconds_spent=open_times_with_seconds_spent+{$times},
			seconds_spent=seconds_spent+{$seconds_spent_dates[$date]}
			where device_id={$device_id}";
			TCClick::app()->db->execute($sql);
		}
	}
}

