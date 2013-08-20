<?php

class ReportsController extends Controller{
	public function filters(){
		return array(
				"LoginRequiredFilter",
				"AdminRequiredFilter + AjaxDailyOpenTimes, AjaxDailySecondsSpentPerOpen",
		);
	}
	
	

	public function actionIndex(){
		$dbMigrateUtil = new DbMigrateUtil();
		$dbMigrateUtil->upgrade();
		$this->render("index");
	}
	
	
	
	
	

	public function actionAjaxHourlyNewDevices(){
		header("Content-type: application/json;charset=utf-8");
		$json = array("stats"=>array(), "dates"=>self::$all_hours, "result"=>"success");
		$today = date("Y-m-d");
		$yesterday = date("Y-m-d", time()-86400);
		$week_ago = date("Y-m-d", time()-7*86400);
		$month_ago = date("Y-m-d", time()-30*86400);
		$today_data = self::generateZeroHourlyCount();
		$yesterday_data = self::generateZeroHourlyCount();
		$week_ago_data = self::generateZeroHourlyCount();
		$month_ago_data = self::generateZeroHourlyCount();
		
		$user = User::current();
		if($user->isAdmin()){
			$sql = "select * from {counter_hourly_new} where channel_id=0
			and `date` in ('$today', '$yesterday', '$week_ago', '$month_ago')";
		}else{
			$channel_ids = $user->getChannelIds();
			if($channel_ids){
				$sql = "select * from {counter_hourly_new} where channel_id in (".join(',', $channel_ids).")
				and `date` in ('$today', '$yesterday', '$week_ago', '$month_ago')";
			}
		}
		foreach(TCClick::app()->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row){
			if($row['date'] == $today){
				$today_data[$row['hour']] += intval($row['count']);
			}elseif($row['date'] == $yesterday){
				$yesterday_data[$row['hour']] += intval($row['count']);
			}elseif($row['date'] == $week_ago){
				$week_ago_data[$row['hour']] += intval($row['count']);
			}elseif($row['date'] == $month_ago){
				$month_ago_data[$row['hour']] += intval($row['count']);
			}
		}
		$json['stats'][] = array("data"=>$today_data, "name"=>"今日","visible"=>true);
		$json['stats'][] = array("data"=>$yesterday_data, "name"=>"昨日","visible"=>true);
		$json['stats'][] = array("data"=>$week_ago_data, "name"=>"7天前","visible"=>false);
		$json['stats'][] = array("data"=>$month_ago_data, "name"=>"30天前","visible"=>false);
		echo json_encode($json);
	}
	
	public function actionAjaxHourlyActiveDevices(){
		header("Content-type: application/json;charset=utf-8");
		$json = array("stats"=>array(), "dates"=>self::$all_hours, "result"=>"success");
		$today = date("Y-m-d");
		$yesterday = date("Y-m-d", time()-86400);
		$week_ago = date("Y-m-d", time()-7*86400);
		$month_ago = date("Y-m-d", time()-30*86400);
		$today_data = self::generateZeroHourlyCount();
		$yesterday_data = self::generateZeroHourlyCount();
		$week_ago_data = self::generateZeroHourlyCount();
		$month_ago_data = self::generateZeroHourlyCount();


		$user = User::current();
		if($user->isAdmin()){
			$sql = "select * from {counter_hourly_active} where channel_id=0
			and `date` in ('$today', '$yesterday', '$week_ago', '$month_ago')";
		}else{
			$channel_ids = $user->getChannelIds();
			if($channel_ids){
				$sql = "select * from {counter_hourly_active} where channel_id in (".join(',', $channel_ids).")
				and `date` in ('$today', '$yesterday', '$week_ago', '$month_ago')";
			}
		}
		foreach(TCClick::app()->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row){
			if($row['date'] == $today){
				$today_data[$row['hour']] += intval($row['count']);
			}elseif($row['date'] == $yesterday){
				$yesterday_data[$row['hour']] += intval($row['count']);
			}elseif($row['date'] == $week_ago){
				$week_ago_data[$row['hour']] += intval($row['count']);
			}elseif($row['date'] == $month_ago){
				$month_ago_data[$row['hour']] += intval($row['count']);
			}
		}
		$json['stats'][] = array("data"=>$today_data, "name"=>"今日","visible"=>true);
		$json['stats'][] = array("data"=>$yesterday_data, "name"=>"昨日","visible"=>true);
		$json['stats'][] = array("data"=>$week_ago_data, "name"=>"7天前","visible"=>false);
		$json['stats'][] = array("data"=>$month_ago_data, "name"=>"30天前","visible"=>false);
		echo json_encode($json);
	}

	public function actionAjaxHourlyOpenTimes(){
		header("Content-type: application/json;charset=utf-8");
		$json = array("stats"=>array(), "dates"=>self::$all_hours, "result"=>"success");
		$today = date("Y-m-d");
		$yesterday = date("Y-m-d", time()-86400);
		$week_ago = date("Y-m-d", time()-7*86400);
		$month_ago = date("Y-m-d", time()-30*86400);
		$today_data = self::generateZeroHourlyCount();
		$yesterday_data = self::generateZeroHourlyCount();
		$week_ago_data = self::generateZeroHourlyCount();
		$month_ago_data = self::generateZeroHourlyCount();
		$sql = "select * from {counter_hourly_open_times} 
		where `date` in ('$today', '$yesterday', '$week_ago', '$month_ago')";
		foreach(TCClick::app()->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row){
			if($row['date'] == $today){
				$today_data[$row['hour']] = intval($row['count']);
			}elseif($row['date'] == $yesterday){
				$yesterday_data[$row['hour']] = intval($row['count']);
			}elseif($row['date'] == $week_ago){
				$week_ago_data[$row['hour']] = intval($row['count']);
			}elseif($row['date'] == $month_ago){
				$month_ago_data[$row['hour']] = intval($row['count']);
			}
		}
		$json['stats'][] = array("data"=>$today_data, "name"=>"今日","visible"=>true);
		$json['stats'][] = array("data"=>$yesterday_data, "name"=>"昨日","visible"=>true);
		$json['stats'][] = array("data"=>$week_ago_data, "name"=>"7天前","visible"=>false);
		$json['stats'][] = array("data"=>$month_ago_data, "name"=>"30天前","visible"=>false);
		echo json_encode($json);
	}
	
	public function actionAjaxDailyNewDevices(){
		header("Content-type: application/json;charset=utf-8");
		$start_date = $_GET['start_date'] ? $_GET['start_date'] : date("Y-m-d", time()-86400*30);
		$end_date = $_GET['end_date'] ? $_GET['end_date'] : date("Y-m-d", time());
		$json = array("stats"=>array(), "result"=>"success");
		$daily_count_with_dates = self::generateZeroDailyCount($start_date, $end_date);
		
		$user = User::current();
		if($user->isAdmin()){
			$sql = "select `date`, `count` from {counter_daily_new} where `date`>=:start and `date`<=:end and channel_id=0";
		}else{
			$channel_ids = $user->getChannelIds();
			if($channel_ids){
				$sql = "select `date`, `count` from {counter_daily_new} where `date`>=:start and `date`<=:end and
				channel_id in (".join(',', $channel_ids).")";
			}
		}
		$stmt = TCClick::app()->db->query($sql, array(":start"=>$start_date, ":end"=>$end_date));
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
			$daily_count_with_dates[$row['date']] += intval($row['count']);
		}
		foreach($daily_count_with_dates as $date=>$count){
			$daily_count[] = $count;
		}
		$json['stats'][] = array("data"=>$daily_count, "name"=>"新增设备");
		$json['dates'] = self::datesArrayForJsonOutput($start_date, $end_date);
		echo json_encode($json);
	}
	
	public function actionAjaxDailyAllDevices(){
		header("Content-type: application/json;charset=utf-8");
		$start_date = $_GET['start_date'] ? $_GET['start_date'] : date("Y-m-d", time()-86400*30);
		$end_date = $_GET['end_date'] ? $_GET['end_date'] : date("Y-m-d", time());
		$json = array("stats"=>array(), "dates"=>self::$all_hours, "result"=>"success");
		$daily_count_with_dates = self::generateZeroDailyCount($start_date, $end_date);
		
		$user = User::current();
		if($user->isAdmin()){
			$sql = "select `date`, all_devices_count as `count` from {counter_daily} where `date`>=:start and `date`<=:end";
			$stmt = TCClick::app()->db->query($sql, array(":start"=>$start_date, ":end"=>$end_date));
			foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
				$daily_count_with_dates[$row['date']] = intval($row['count']);
			}
		}else{
			$channel_ids = $user->getChannelIds();
			if($channel_ids){
				$sql = "select `date`, `count` from {counter_daily_new} where `date`<=:end and
				channel_id in (".join(',', $channel_ids).") order by date";
				
				$sum = 0;
				$stmt = TCClick::app()->db->query($sql, array(":end"=>$end_date));
				foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
					$sum += $row['count'];
					if(isset($daily_count_with_dates[$row['date']])){
						$daily_count_with_dates[$row['date']] = $sum;
					}
				}
				$prev_daily_count = 0; // 如果某一天这些渠道没有新增用户，那么select出来的行里面会没有这一天的数据，要进行修复
				foreach($daily_count_with_dates as $date=>$count){
					if($count == 0) $daily_count_with_dates[$date] = $prev_daily_count;
					else $prev_daily_count = $count;
				}
			}
		}
		
		foreach($daily_count_with_dates as $date=>$count){
			$daily_count[] = $count;
		}
		$json['stats'][] = array("data"=>$daily_count, "name"=>"累计设备");
		$json['dates'] = self::datesArrayForJsonOutput($start_date, $end_date);
		echo json_encode($json);
	}
	
	public function actionAjaxDailyActiveDevices(){
		header("Content-type: application/json;charset=utf-8");
		$today = date("Y-m-d");
		$start_date = $_GET['start_date'] ? $_GET['start_date'] : date("Y-m-d", time()-86400*30);
		$end_date = $_GET['end_date'] ? $_GET['end_date'] : $today;
		$json = array("stats"=>array(), "dates"=>self::$all_hours, "result"=>"success");
		$daily_count_with_dates_active = self::generateZeroDailyCount($start_date, $end_date);
		$daily_count_with_dates_old = self::generateZeroDailyCount($start_date, $end_date);
		$daily_count_with_dates_new = self::generateZeroDailyCount($start_date, $end_date);
		
		$user = User::current();
		if($user->isAdmin()){
			$sql_new = "select `date`, `count` from {counter_daily_new} where `date`>=:start and `date`<=:end and channel_id=0";
			$sql_active = "select `date`, `count` from {counter_daily_active} where `date`>=:start and `date`<=:end and channel_id=0";
		}else{
			$channel_ids = $user->getChannelIds();
			if($channel_ids){
				$sql_new = "select `date`, `count` from {counter_daily_new} where `date`>=:start and `date`<=:end and
				channel_id in (".join(',', $channel_ids).")";
				$sql_active = "select `date`, `count` from {counter_daily_active} where `date`>=:start and `date`<=:end and
				channel_id in (".join(',', $channel_ids).")";
			}
		}
		$stmt = TCClick::app()->db->query($sql_active, array(":start"=>$start_date, ":end"=>$end_date));
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
			$daily_count_with_dates_active[$row['date']] += intval($row['count']);
		}
		$stmt = TCClick::app()->db->query($sql_new, array(":start"=>$start_date, ":end"=>$end_date));
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
			$daily_count_with_dates_new[$row['date']] += intval($row['count']);
			$daily_count_with_dates_old[$row['date']] = $daily_count_with_dates_active[$row['date']] - $daily_count_with_dates_new[$row['date']];
		}
		
		foreach($daily_count_with_dates_new as $date=>$count) $daily_count_new[] = $count;
		foreach($daily_count_with_dates_old as $date=>$count) $daily_count_old[] = $count;
		$json['stats'][] = array("data"=>$daily_count_new, "name"=>"新设备");
		$json['stats'][] = array("data"=>$daily_count_old, "name"=>"老设备");
		$json['dates'] = self::datesArrayForJsonOutput($start_date, $end_date);
		echo json_encode($json);
	}
	
	public function actionAjaxDailyOpenTimes(){
		header("Content-type: application/json;charset=utf-8");
		$today = date("Y-m-d");
		$start_date = $_GET['start_date'] ? $_GET['start_date'] : date("Y-m-d", time()-86400*30);
		$end_date = $_GET['end_date'] ? $_GET['end_date'] : $today;
		$json = array("stats"=>array(), "dates"=>self::$all_hours, "result"=>"success");
		$daily_count_with_dates = self::generateZeroDailyCount($start_date, $end_date);
		$sql = "select `date`, open_times as `count` from {counter_daily} where `date`>=:start and `date`<=:end";
		$stmt = TCClick::app()->db->query($sql, array(":start"=>$start_date, ":end"=>$end_date));
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
			$daily_count_with_dates[$row['date']] = intval($row['count']);
		}
		
		// 今天的启动次数，没有在表 counter_daily 中做实时记录，从表 daily_active_devices_** 中去做计算
		if (isset($daily_count_with_dates[$today])){
			$tablename = 'daily_active_devices_' . str_replace('-', '_', $today);
			$sql = "select sum(open_times) from {{$tablename}}";
			$daily_count_with_dates[$today] = intval(TCClick::app()->db->query($sql)->fetchColumn());
		}
		
		
		foreach($daily_count_with_dates as $date=>$count){
			$daily_count[] = $count;
		}
		$json['stats'][] = array("data"=>$daily_count, "name"=>"启动次数");
		$json['dates'] = self::datesArrayForJsonOutput($start_date, $end_date);
		echo json_encode($json);
	}
	
	public function actionAjaxDailySecondsSpentPerOpen(){
		header("Content-type: application/json;charset=utf-8");
		$start_date = $_GET['start_date'] ? $_GET['start_date'] : date("Y-m-d", time()-86400*30);
		$end_date = $_GET['end_date'] ? $_GET['end_date'] : date("Y-m-d", time());
		$json = array("stats"=>array(), "dates"=>self::$all_hours, "result"=>"success");
		$daily_count_with_dates = self::generateZeroDailyCount($start_date, $end_date);
		$sql = "select `date`, seconds_spent, open_times_with_seconds_spent 
		from {counter_daily} where `date`>=:start and `date`<=:end";
		$stmt = TCClick::app()->db->query($sql, array(":start"=>$start_date, ":end"=>$end_date));
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
			if($row['open_times_with_seconds_spent'] > 0){
				$daily_count_with_dates[$row['date']] = intval(
						$row['seconds_spent'] / $row['open_times_with_seconds_spent']
				);
			}else{
				$daily_count_with_dates[$row['date']] = 0;
			}
		}
		$today = date("Y-m-d");
		if($today>=$start_date && $today<=$end_date){
			$daily_count_with_dates[$today] = intval(TCClickCounter::calculateSecondsSpentPerOpen($today));
		}
		foreach($daily_count_with_dates as $date=>$count){
			$daily_count[] = $count;
		}
		$json['stats'][] = array("data"=>$daily_count, "name"=>"平均每次使用时长");
		$json['dates'] = self::datesArrayForJsonOutput($start_date, $end_date);
		echo json_encode($json);
	}
	
	
	
	
	public static function generateZeroDailyCount($start_date, $end_date){
		$start_time = strtotime($start_date);
		$end_time = strtotime($end_date);
		$daily_count = array();
		for($time=$start_time; $time<=$end_time; $time+=86400){
			$daily_count[date("Y-m-d", $time)] = 0;
		}
		return $daily_count;
	}
	
	public static function generateZeroHourlyCount(){
		$hourly_count = array();
		for($i=0; $i<24; $i++){
			$hourly_count[] = 0;
		}
		return $hourly_count;
	}
	
	public static function datesArrayForJsonOutput($start_date, $end_date){
		$start_time = strtotime($start_date);
		$end_time = strtotime($end_date);
		$dates = array();
		for($time=$start_time; $time<=$end_time; $time+=86400){
			$dates[] = date("m-d", $time);
		}
		return $dates;
	}
	
	public static $all_hours = array("00", "01", "02", "03", "04", "05",
			"06", "07", "08", "09", "10", "11",
			"12", "13", "14", "15", "16", "17",
			"18", "19", "20", "21", "22", "23");
}

