<?php

class EventsController extends  Controller{
	public function filters(){
		return array("AdminRequiredFilter");
	}

	public function actionIndex(){
		$this->render('index');
	}
	
	public function actionView(){
		$event = Event::loadById($_GET['id']);
		if($event){
			$this->render('view', array('event'=>$event));
		}else{
			$this->redirect(TCClick::app()->root_url . 'events');
		}
	}
	
	public function actionAjaxDailyCounts(){
		//header("Content-type: application/json;charset=utf-8");
		$today = date("Y-m-d");
		$start_date = $_GET['from'] ? $_GET['from'] : date("Y-m-d", time()-86400*30);
		$end_date = $today;
		$version = $_GET['version_id']?$_GET['version_id']:0;
		
		$sql= "select * from {event_params} where event_id ={$_GET['event_id']} limit 1";
		$stmt = TCClick::app()->db->query($sql);
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
			$default_param = $row['param_id'];
		}
		$param = $_GET['param_id']?$_GET['param_id']:$default_param;
		$date = self::datesArrayForJsonOutput($start_date, $end_date);
	  $json = array("stats"=>array(), "dates"=>$date, "result"=>"success");
	  $daily_count_with_dates = array();
	  
	  if($version){
	  	$sql = "select * from {counter_daily_events} where event_id={$_GET['event_id']}
	  	and version_id={$version} and param_id={$param} and date>='$from'";
	  }else{
	  $sql = "select * from {counter_daily_events} where event_id={$_GET['event_id']}
	  		and param_id={$param} and date>='$from'";
	  }
	  
	  $stmt = TCClick::app()->db->query($sql);
	  foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
	  	if(!$daily_count_with_dates[$row['value_id']]){//初始化
	  		$daily_count_with_dates[$row['value_id']] =self::generateZeroDailyCount($start_date, $end_date);
	  	}
	  	$daily_count_with_dates[$row['value_id']][$row['date']] += intval($row['count']);
	  }
	  
	  $all_count = array();
	  
	  foreach($daily_count_with_dates as $key=>$count_data){
	  	foreach($count_data as $date=>$count) {
	  		$all_count[$date] +=$count;
	  	}
	  }
	  
	  foreach($daily_count_with_dates as $key=>$count_data){
	  	$daily_count = array();
	  	foreach($count_data as $date=>$count) {
	  		$daily_count[] = round($count/$all_count[$date],5);
	  	}
	  	$json['stats'][] = array("data"=>$daily_count, "name"=>EventName::nameOf($key));
	  }
	  //print_r($all_count);
	  print_r($daily_count_with_dates);
		//echo json_encode($json);
	}
	
	public function actionAjaxDailyCountsSpline(){
		header("Content-type: application/json;charset=utf-8");
		$today = date("Y-m-d");
		$start_date = $_GET['from'] ? $_GET['from'] : date("Y-m-d", time()-86400*30);
		$end_date = $today;
		$version = $_GET['version_id']?$_GET['version_id']:0;
		
		$sql= "select * from {event_params} where event_id ={$_GET['event_id']} limit 1";
		$stmt = TCClick::app()->db->query($sql);
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
			$default_param = $row['param_id'];
		}
		$param = $_GET['param_id']?$_GET['param_id']:$default_param;
		$date = self::datesArrayForJsonOutput($start_date, $end_date);
		$json = array("stats"=>array(), "dates"=>$date, "result"=>"success");
		$daily_count_with_dates = array();
		 
		if($version){
			$sql = "select * from {counter_daily_events} where event_id={$_GET['event_id']}
			and version_id={$version} and param_id={$param} and date>='$from'";
		}else{
		$sql = "select * from {counter_daily_events} where event_id={$_GET['event_id']}
				and param_id={$param} and date>='$from'";
		}
		
		$stmt = TCClick::app()->db->query($sql);
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
			if(!$daily_count_with_dates[$row['value_id']]){//初始化
				$daily_count_with_dates[$row['value_id']] =self::generateZeroDailyCount($start_date, $end_date);
			}
			$daily_count_with_dates[$row['value_id']][$row['date']] += intval($row['count']);
		}
		
		foreach($daily_count_with_dates as $key=>$count_data){
			$daily_count = array();
			foreach($count_data as $date=>$count) {
				$daily_count[] = $count;
			}
			$json['stats'][] = array("data"=>$daily_count, "name"=>EventName::nameOf($key));
		}
		echo json_encode($json);
		 
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
	
	public static function generateZeroDailyCount($start_date, $end_date){
		$start_time = strtotime($start_date);
		$end_time = strtotime($end_date);
		$daily_count = array();
		for($time=$start_time; $time<=$end_time; $time+=86400){
			$daily_count[date("Y-m-d", $time)] = 0;
		}
		return $daily_count;
	}
	
}

