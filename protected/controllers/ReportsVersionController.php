<?php

include_once "ReportsController.php";
class ReportsVersionController extends Controller{
	public function filters(){
		return array("LoginRequiredFilter");
	}
	
	

	public function actionIndex(){
		$this->render("index");
	}
	
	
	public function actionAjaxDailyNewDevices(){
		header("Content-type: application/json;charset=utf-8");
		$start_date = $_GET['start_date'] ? $_GET['start_date'] : date("Y-m-d", time()-86400*30);
		$end_date = $_GET['end_date'] ? $_GET['end_date'] : date("Y-m-d", time());
		$json = array("stats"=>array(), "result"=>"success");
		$daily_count_with_dates = array();
		$sql = "select `date`, `count`, version_id from {counter_daily_new_version}
		where `date`>=:start and `date`<=:end order by count desc";
		$stmt = TCClick::app()->db->query($sql, array(":start"=>$start_date, ":end"=>$end_date));
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
			if(!$daily_count_with_dates[$row['version_id']]){
				$daily_count_with_dates[$row['version_id']] = ReportsController::generateZeroDailyCount($start_date, $end_date);
			}
			$daily_count_with_dates[$row['version_id']][$row['date']] = intval($row['count']);
		}
		foreach($daily_count_with_dates as $version_id=>$version_data){
			$daily_count = array();
			foreach($version_data as $date=>$count) $daily_count[] = $count;
			$json['stats'][] = array("data"=>$daily_count, "name"=>Version::nameOf($version_id));
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
		$sql = "select `date`, `count`, version_id from {counter_daily_active_version}
		where `date`>=:start and `date`<=:end order by count desc";
		$stmt = TCClick::app()->db->query($sql, array(":start"=>$start_date, ":end"=>$end_date));
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
			if(!$daily_count_with_dates[$row['version_id']]){
				$daily_count_with_dates[$row['version_id']] = ReportsController::generateZeroDailyCount($start_date, $end_date);
			}
			$daily_count_with_dates[$row['version_id']][$row['date']] = intval($row['count']);
		}
		foreach($daily_count_with_dates as $version_id=>$version_data){
			$daily_count = array();
			foreach($version_data as $date=>$count) $daily_count[] = $count;
			$json['stats'][] = array("data"=>$daily_count, "name"=>Version::nameOf($version_id));
		}
		$json['dates'] = ReportsController::datesArrayForJsonOutput($start_date, $end_date);
		echo json_encode($json);
	}
	

	public function actionAjaxDailyUpdateDevices(){
		header("Content-type: application/json;charset=utf-8");
		$start_date = $_GET['start_date'] ? $_GET['start_date'] : date("Y-m-d", time()-86400*30);
		$end_date = $_GET['end_date'] ? $_GET['end_date'] : date("Y-m-d", time());
		$json = array("stats"=>array(), "result"=>"success");
		$daily_count_with_dates = array();
		$sql = "select `date`, `count`, version_id from {counter_daily_update_with_version}
		where `date`>=:start and `date`<=:end order by count desc";
		$stmt = TCClick::app()->db->query($sql, array(":start"=>$start_date, ":end"=>$end_date));
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
			if(!$daily_count_with_dates[$row['version_id']]){
				$daily_count_with_dates[$row['version_id']] = ReportsController::generateZeroDailyCount($start_date, $end_date);
			}
			$daily_count_with_dates[$row['version_id']][$row['date']] = intval($row['count']);
		}
		foreach($daily_count_with_dates as $version_id=>$version_data){
			$daily_count = array();
			foreach($version_data as $date=>$count) $daily_count[] = $count;
			$json['stats'][] = array("data"=>$daily_count, "name"=>Version::nameOf($version_id));
		}
		$json['dates'] = ReportsController::datesArrayForJsonOutput($start_date, $end_date);
		echo json_encode($json);
	}
}


