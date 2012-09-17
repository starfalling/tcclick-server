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
    $start_date = date("Y-m-d", time() - 86400 * 30);
    $end_date = date('Y-m-d', time());
    $day_data = array(); // 每日活跃设备
    for($i = 30; $i>=0; $i --){
      $date = date("Y-m-d", time() - 86400 * $i);
      $sql = "select `count` from {counter_daily_active} where date='$date' and channel_id=0";
      $stmt = TCClick::app()->db->query($sql)->fetchColumn();
      $day_data[] = intval($stmt);
    }
    $json['stats'][] = array("data" => $day_data, "name" => '活跃设备');
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

    	$weekly_device_counts[] = $this->weeklyActiveCount($date);
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
    		$date = sprintf("%d-%02d", $y, $m);
    	}else{
    		$y = $year; $m = $month-$i;
    		$monthly_dates[] = sprintf("%d年%d月", $y, $m);
    		$date = sprintf("%d-%02d", $y, $m);
    	}
    	$monthly_device_counts[] = $this->monthlyActiveCount($date);
    }
    $json['stats'][] = array("data"=>$monthly_device_counts, "name"=>'月活跃设备', "visible"=>true);
    $json['dates'] = $monthly_dates;
    echo json_encode($json);
  }
  
  //周活跃设备率
  public function actionAjaxActiveWeekRate(){
    header("Content-type: application/json; chartset = utf-8");
    $json = array("stats" => array(), "dates" => array(), "result" => "success");
    $weekly_dates = array();
    for($i = 30; $i > 0; $i --){
      if(date("w", time() - 86400 * $i) == 1){
        $dates[] = date('m-d',time() - 86400 * $i);
        $weekly_dates[] =  time() - 86400 * $i;
      }
    }
    foreach($weekly_dates as $week_time){
      $week = date('Y-m-d' , strtotime("1 week",$week_time));
      $sql = "select id from {devices} where created_at < '$week 00:00:00' ORDER BY  id DESC LIMIT 1";
      $device_count = TCClick::app()->db->query($sql)->fetchColumn(); // 上一个周的所有的设备
      $weekly_device_count = self::weeklyActiveCount($week_time);
      $weekly_device_rate[] = round($weekly_device_count / $device_count, 2) *100;
    }
    $json['stats'][] = array("data" => $weekly_device_rate, "name" => "上周活跃率", "visible" => true);
    $json['dates'] = $dates;
    echo json_encode($json);
  }
  
  //月活跃率
  public function actionAjaxActiveMonthRate(){
    header("Content-type: application/json; chartset = utf-8");
    $json = array("stats" => array(), "dates" => array(), "result" => "success");
    $month = date("Y-m", time());
    $sql = "select id  from {devices} where created_at< '{$month}-01 00:00:00' ORDER BY ID  DESC LIMIT 1 ";
    $device_count = TCClick::app()->db->query($sql)->fetchColumn();
    $monthly_active_devices_count = self::monthActiveCount();
    $month_active_rate = round($monthly_active_devices_count / $device_count, 2)*100;
    $json['stats'][] = array("data" => array($month_active_rate), "name" => "上月活跃率", "visoble" => true);
    $json['dates'] = array(date("m", strtotime("-1 month",time())) . "-01");
    echo json_encode($json);
  }
  
  protected  static  function monthlyActiveCount($month_start_date){
  	// 查下看计数器里面有没有
  	$sql = "select `count` from {counter_monthly_active} where channel_id=0 and date='{$month_start_date}'";
  	$stmt = TCClick::app()->db->query($sql);
  	if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) != null){
  		return intval($row['count']);
  	}
  	
    $table_name = str_replace("-", "_", "{monthly_active_devices_{$month_start_date}}");
    $sql = "select count(*) from $table_name";
    return intval(TCClick::app()->db->query($sql)->fetchColumn());
  }
  
  public function weeklyActiveCount($week_start_date){
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
}