<?php

include_once "ReportsController.php";
class ReportsSecondsSpentController extends Controller{
	public function filters(){
		return array("AdminRequiredFilter");
	}
	
	

	public function actionIndex(){
		$this->render("index");
	}
	
	public function actionAjaxPerOpen(){
		header("Content-type: application/json;charset=utf-8");
		$json = array("stats"=>array(), "result"=>"success");
		$json['dates'] = TCClick::$all_seconds_spent_names;
		$yesterday_data = array(0, 0, 0, 0, 0, 0, 0, 0);
		$yesterday_sum_count = 0;
		$compare_to_data = array(0, 0, 0, 0, 0, 0, 0, 0);
		$compare_to_sum_count = 0;
		$yesterday = date("Y-m-d", time()-86400);
		$compare_to = '';
		if($_GET['compare_to']) $compare_to = date("Y-m-d", strtotime($_GET['compare_to']));
		if($compare_to == $yesterday) $compare_to = '';
		
		$sql = "select * from {counter_daily_seconds_spent_per_open} where date in ('{$yesterday}', '$compare_to')";
		$stmt = TCClick::app()->db->query($sql);
		foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
			if($row['date'] == $yesterday){
				$yesterday_sum_count += $row['count'];
				$yesterday_data[$row['seconds_spent_id']-1] = intval($row['count']);
			}else{
				$compare_to_data[$row['seconds_spent_id']-1] = intval($row['count']);
				$compare_to_sum_count += $row['count'];
			}
		}
		if($yesterday_sum_count){
			foreach($yesterday_data as $i=>$count)
				$yesterday_data[$i] = $count / $yesterday_sum_count;
		}
		if($compare_to_sum_count){
			foreach($compare_to_data as $i=>$count)
				$compare_to_data[$i] = $count / $compare_to_sum_count;
		}
		$json['stats'][] = array("data"=>$yesterday_data, "name"=>$yesterday);
		if($compare_to) $json['stats'][] = array("data"=>$compare_to_data, "name"=>$compare_to);
		echo json_encode($json);
	}
	
	public function actionAjaxPerday(){
	  header("Content-type: application/json;charset=utf-8");
	  $json = array("stats"=>array(), "result"=>"success");
	  $json['dates'] = TCClick::$all_seconds_spent_names;
	  $yesterday_data = array(0, 0, 0, 0, 0, 0, 0, 0);
	  $yesterday_sum_count = 0;
	  $compare_to_data = array(0, 0, 0, 0, 0, 0, 0, 0);
	  $compare_to_sum_count = 0;
	  $yesterday = date("Y-m-d", time()-86400);
	  $compare_to = '';
	  if($_GET['compare_to']) $compare_to = date("Y-m-d", strtotime($_GET['compare_to']));
	  if($compare_to == $yesterday) $compare_to = '';
	  
	  $sql = "select * from {counter_daily_seconds_spent_per_day} where date in ('{$yesterday}', '$compare_to')";
	  $stmt = TCClick::app()->db->query($sql);
	  foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
	    if($row['date'] == $yesterday){
	      $yesterday_sum_count += $row['count'];
	      $yesterday_data[$row['seconds_spent_id']-1] = intval($row['count']);
	    }else{
	      $compare_to_data[$row['seconds_spent_id']-1] = intval($row['count']);
	      $compare_to_sum_count += $row['count'];
	    }
	  }
	  if($yesterday_sum_count){
	    foreach($yesterday_data as $i=>$count)
	      $yesterday_data[$i] = $count / $yesterday_sum_count;
	  }
	  if($compare_to_sum_count){
	    foreach($compare_to_data as $i=>$count)
	      $compare_to_data[$i] = $count / $compare_to_sum_count;
	  }
	  $json['stats'][] = array("data"=>$yesterday_data, "name"=>$yesterday);
	  if($compare_to) $json['stats'][] = array("data"=>$compare_to_data, "name"=>$compare_to);
	  echo json_encode($json);
	  }
}

