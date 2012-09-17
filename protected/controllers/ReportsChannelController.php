<?php

include_once "ReportsController.php";
class ReportsChannelController extends Controller{
	public function filters(){
		return array("LoginRequiredFilter");
	}
	
	
	public function actionIndex(){
		$this->render("index");
	}

	public function actionAjaxHourlyNewDevices(){
		header("Content-type: application/json;charset=utf-8");
		$json = array("stats"=>array(), "dates"=>ReportsController::$all_hours, "result"=>"success");
		$date = $_GET['date'] ? $_GET['date'] : date("Y-m-d");
		$sql = "select * from {counter_hourly_new} where date=:date and channel_id<>0
		order by count desc";
		$stmt = TCClick::app()->db->query($sql, array(":date"=>$date));
		$hourly_counts = array();
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
			if(!$hourly_counts[$row['channel_id']]){
				$hourly_counts[$row['channel_id']] = ReportsController::generateZeroHourlyCount();
			}
			$hourly_counts[$row['channel_id']][$row['hour']] = intval($row['count']);
		}
		
		$output_channels_count = 0; // 只展示前10名的渠道
		foreach($hourly_counts as $channel_id=>$data){
			$json['stats'][] = array("data"=>$data, "name"=>Channel::nameOf($channel_id), "visible"=>true);
			$output_channels_count++;
			if($output_channels_count >= 10) break;
		}
		
		echo json_encode($json);
	}

	public function actionAjaxHourlyActiveDevices(){
		header("Content-type: application/json;charset=utf-8");
		$json = array("stats"=>array(), "dates"=>ReportsController::$all_hours, "result"=>"success");
		$date = $_GET['date'] ? $_GET['date'] : date("Y-m-d");
		$sql = "select * from {counter_hourly_active} where date=:date and channel_id<>0
		order by count desc";
		$stmt = TCClick::app()->db->query($sql, array(":date"=>$date));
		$hourly_counts = array();
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
			if(!$hourly_counts[$row['channel_id']]){
				$hourly_counts[$row['channel_id']] = ReportsController::generateZeroHourlyCount();
			}
			$hourly_counts[$row['channel_id']][$row['hour']] = intval($row['count']);
		}
		
		$output_channels_count = 0; // 只展示前10名的渠道
		foreach($hourly_counts as $channel_id=>$data){
			$json['stats'][] = array("data"=>$data, "name"=>Channel::nameOf($channel_id), "visible"=>true);
			$output_channels_count++;
			if($output_channels_count >= 10) break;
		}
		
		echo json_encode($json);
	}
	
	public function actionAjaxDailyNewDevices(){
		header("Content-type: application/json;charset=utf-8");
		$start_date = $_GET['start_date'] ? $_GET['start_date'] : date("Y-m-d", time()-86400*30);
		$end_date = $_GET['end_date'] ? $_GET['end_date'] : date("Y-m-d", time());
		$json = array("stats"=>array(), "result"=>"success");
		$daily_count_with_dates = array();
		$sql = "select `date`, `count`, channel_id from {counter_daily_new}
		where `date`>=:start and `date`<=:end and channel_id<>0 order by count desc";
		$stmt = TCClick::app()->db->query($sql, array(":start"=>$start_date, ":end"=>$end_date));
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
			if(!$daily_count_with_dates[$row['channel_id']]){
				$daily_count_with_dates[$row['channel_id']] = ReportsController::generateZeroDailyCount($start_date, $end_date);
			}
			$daily_count_with_dates[$row['channel_id']][$row['date']] = intval($row['count']);
		}
		foreach($daily_count_with_dates as $channel_id=>$channel_data){
			$daily_count = array();
			foreach($channel_data as $date=>$count) $daily_count[] = $count;
			$json['stats'][] = array("data"=>$daily_count, "name"=>Channel::nameOf($channel_id));
		}
		$json['dates'] = ReportsController::datesArrayForJsonOutput($start_date, $end_date);
		echo json_encode($json);
	}
	
	public function actionAjaxDailyActiveDevices(){
		header("Content-type: application/json;charset=utf-8");
		$start_date = $_GET['start_date'] ? $_GET['start_date'] : date("Y-m-d", time()-86400*30);
		$end_date = $_GET['end_date'] ? $_GET['end_date'] : date("Y-m-d", time());
		$json = array("stats"=>array(), "result"=>"success");
		$daily_count_with_dates = array();
		$sql = "select `date`, `count`, channel_id from {counter_daily_active}
		where `date`>=:start and `date`<=:end and channel_id<>0 order by count desc";
		$stmt = TCClick::app()->db->query($sql, array(":start"=>$start_date, ":end"=>$end_date));
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
			if(!$daily_count_with_dates[$row['channel_id']]){
				$daily_count_with_dates[$row['channel_id']] = ReportsController::generateZeroDailyCount($start_date, $end_date);
			}
			$daily_count_with_dates[$row['channel_id']][$row['date']] = intval($row['count']);
		}
		foreach($daily_count_with_dates as $channel_id=>$channel_data){
			$daily_count = array();
			foreach($channel_data as $date=>$count) $daily_count[] = $count;
			$json['stats'][] = array("data"=>$daily_count, "name"=>Channel::nameOf($channel_id));
		}
		$json['dates'] = ReportsController::datesArrayForJsonOutput($start_date, $end_date);
		echo json_encode($json);
	}
}

