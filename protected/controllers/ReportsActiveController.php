<?php
include_once "ReportsController.php";
class ReportsActiveController extends  Controller{
	public function filters(){
		return array("LoginRequiredFilter");
	}
	
	
  public function actionIndex(){
    $this->render('index');
  }
  
  //日活跃设备
  public function actionAjaxActiveDaily(){
    header("Content-type: application/json;charset=utf-8");
    $json = array("stats" => array(), "dates" => array(), "result" => "success");
		$start_date = $_GET['from'] ? $_GET['from'] : date("Y-m-d", time()-86400*30);
		$end_date = $_GET['to'] ? $_GET['to'] : date("Y-m-d", time());
		
		$date_counts = ReportsController::generateZeroDailyCount($start_date, $end_date);
		$sql = "select `date`, `count` from {counter_daily_active}
		where  `date`>=:start and `date`<=:end and channel_id=0";
		$stmt = TCClick::app()->db->query($sql, array(":start"=>$start_date, ":end"=>$end_date));
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
			$date_counts[$row['date']] = intval($row['count']);
		}
		$data = array();
		foreach($date_counts as $date=>$count) $data[] = $count;
    $json['stats'][] = array("data" => $data, "name" => '活跃设备');
    $json['dates'] = ReportsController::datesArrayForJsonOutput($start_date, $end_date);
    echo json_encode($json);
  }
  
  //周活跃设备
  public function actionAjaxActiveWeekly(){
    header("Content-type: application/json;chartset=utf-8");
    $json = array("result" => "success");
    $w = date("w");
    $w = $w==0 ? 7 : $w; // 今天是一周当中的周几，取值 1-7
    $time_of_today_start = strtotime(date("Y-m-d"));
    $time_of_week_start_of_today = $time_of_today_start - ($w-1) * 86400;
    
    $weekly_dates = array();
    $weekly_device_counts = array();
    for($i=10; $i>=0; $i--){
    	$time = $time_of_week_start_of_today - $i*86400*7;
    	$date = date("Y-m-d", $time);
    	$weekly_dates[] = date("m-d", $time);

    	$weekly_device_counts[] = $this->calculateWeeklyActiveCount($date);
    }
    $json['stats'][] = array("data"=>$weekly_device_counts, "name"=>'周活跃设备', "visible"=>true);
    $json['dates'] = $weekly_dates;
    echo json_encode($json);
  }
  
  //月活跃设备
  public function actionAjaxActiveMonthly(){
    header("Content-type: application/json;chartset=utf-8");
    $json = array("result" => "success");
    
    $monthly_dates = array();
    $monthly_device_counts = array();
    $year = date("Y"); $month = date("m");
    for($i=12; $i>=0; $i--){
    	if($month<=$i){
    		$y = $year-1;
    		$m = 12+$month-$i;
    		$monthly_dates[] = sprintf("%d年%d月", $y, $m);
    		$date = sprintf("%d-%02d-01", $y, $m);
    	}else{
    		$y = $year; $m = $month-$i;
    		$monthly_dates[] = sprintf("%d年%d月", $y, $m);
    		$date = sprintf("%d-%02d-01", $y, $m);
    	}
    	$monthly_device_counts[] = $this->calculateMonthlyActiveCount($date);
    }
    $json['stats'][] = array("data"=>$monthly_device_counts, "name"=>'月活跃设备', "visible"=>true);
    $json['dates'] = $monthly_dates;
    echo json_encode($json);
  }
  
  //周活跃设备率
  public function actionAjaxActiveWeekRate(){
    header("Content-type: application/json;chartset=utf-8");
    $json = array("result" => "success");
    $w = date("w");
    $w = $w==0 ? 7 : $w; // 今天是一周当中的周几，取值 1-7
    $time_of_today_start = strtotime(date("Y-m-d"));
    $time_of_week_start_of_today = $time_of_today_start - ($w-1) * 86400;
    
    $weekly_dates = array();
    $weekly_active_rates = array();
    for($i=10; $i>=0; $i--){
    	$time = $time_of_week_start_of_today - $i*86400*7;
    	$date = date("Y-m-d", $time);
    	$weekly_dates[] = date("m-d", $time);

    	$weekly_active_rates[] = $this->calculateWeeklyActiveRate($date);
    }
    $json['stats'][] = array("data"=>$weekly_active_rates, "name"=>'周活跃率', "visible"=>true);
    $json['dates'] = $weekly_dates;
    echo json_encode($json);
  }
  
  //月活跃率
  public function actionAjaxActiveMonthRate(){
    header("Content-type: application/json;chartset=utf-8");
    $json = array("result" => "success");
    
    $monthly_dates = array();
    $monthly_active_rates = array();
    $year = date("Y"); $month = date("m");
    for($i=12; $i>=0; $i--){
    	if($month<=$i){
    		$y = $year-1;
    		$m = 12+$month-$i;
    		$monthly_dates[] = sprintf("%d年%d月", $y, $m);
    		$date = sprintf("%d-%02d-01", $y, $m);
    	}else{
    		$y = $year; $m = $month-$i;
    		$monthly_dates[] = sprintf("%d年%d月", $y, $m);
    		$date = sprintf("%d-%02d-01", $y, $m);
    	}
    	$monthly_active_rates[] = $this->calculateMonthlyActiveRate($date);
    }
    $json['stats'][] = array("data"=>$monthly_active_rates, "name"=>'月活跃率', "visible"=>true);
    $json['dates'] = $monthly_dates;
    echo json_encode($json);
  }
	
	
	
	
  
  private  function calculateMonthlyActiveCount($month_start_date){
  	// 查下看计数器里面有没有
  	$sql = "select `count` from {counter_monthly_active} where channel_id=0 and date='{$month_start_date}'";
  	$stmt = TCClick::app()->db->query($sql);
  	if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) != null){
  		return intval($row['count']);
  	}
  	
		$table_name = '{monthly_active_devices_' . date('Y_m', strtotime($month_start_date)) . '}';
    $sql = "select count(*) from $table_name";
    return intval(TCClick::app()->db->query($sql)->fetchColumn());
  }
	
	private function calculateMonthlyActiveRate($month_start_date){
		$active_count = $this->calculateMonthlyActiveCount($month_start_date);
		$all_devices_count = 0;
		$rate = 0;
		$month_end_date = date("Y-m-d", strtotime("+1 month", strtotime($month_start_date)) - 86400);
		$today = date("Y-m-d");
		if($week_end_date > $today){ // 今天所在的这一个月，直接取当前总激活设备数
			$sql = 'select count(*) from {devices}';
			$all_devices_count = TCClick::app()->db->query($sql)->fetchColumn(); 
		}else{
			$sql = "select all_devices_count from {counter_daily} where date='{$month_end_date}'";
			$all_devices_count = intval(TCClick::app()->db->query($sql)->fetchColumn());
		}
		if($all_devices_count) $rate = $active_count / $all_devices_count;
		if($rate > 1) $rate = 1;
		return $rate;
	}
  
  private function calculateWeeklyActiveCount($week_start_date){
  	// 查下看计数器里面有没有
  	$sql = "select `count` from {counter_weekly_active} where channel_id=0 and date='{$week_start_date}'";
  	$stmt = TCClick::app()->db->query($sql);
  	if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) != null){
  		return intval($row['count']);
  	}
  	
    $table_name = str_replace("-", "_", "{weekly_active_devices_{$week_start_date}}");
    $sql = "select count(*) from $table_name";
    return intval(TCClick::app()->db->query($sql)->fetchColumn());
  }
	
	private function calculateWeeklyActiveRate($week_start_date){
		$active_count = $this->calculateWeeklyActiveCount($week_start_date);
		$all_devices_count = 0;
		$rate = 0;
		$week_end_date = date("Y-m-d", strtotime($week_start_date) + 86400*7);
		$today = date("Y-m-d");
		if($week_end_date > $today){ // 今天所在的这一周，直接取当前总激活设备数
			$sql = 'select count(*) from {devices}';
			$all_devices_count = TCClick::app()->db->query($sql)->fetchColumn(); 
		}else{
			$sql = "select all_devices_count from {counter_daily} where date='{$week_end_date}'";
			$all_devices_count = intval(TCClick::app()->db->query($sql)->fetchColumn());
		}
		if($all_devices_count) $rate = $active_count / $all_devices_count;
		if($rate > 1) $rate = 1;
		return $rate;
	}
}