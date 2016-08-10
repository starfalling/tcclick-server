<?php
include_once "ReportsController.php";

class ExceptionsController extends  Controller{
	public function filters(){
		return array(
				"LoginRequiredFilter - AjaxListUnLocated,AjaxView,AjaxSetLocatedContent",
				"ExternalAccessFilter - index, view, update",
		);
	}
	
	
  public function actionIndex(){
    $this->renderCompatibleWithExternalSite('index');
  }
  
  public function actionView(){
  	if(!is_numeric($_GET['id'])) return header("Location: ".TCClick::app()->root_url."exceptions");
  	$sql = "select * from {exceptions} where id={$_GET['id']}";
  	$exception = TCClick::app()->db->query($sql)->fetch(PDO::FETCH_ASSOC);
  	if(!$exception) return header("Location: ".TCClick::app()->root_url."exceptions");
  	$this->renderCompatibleWithExternalSite('view', array("exception"=>$exception));
  }
  
  public function actionAjaxListUnLocated(){
    header("Content-type: application/json;charset=utf-8");
  	$sql = "select id, version_id from {exceptions} where located=0";
  	$items = TCClick::app()->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  	foreach($items as &$item){
  		$item['version'] = Version::nameOf($item['version_id']);
  		unset($item['version_id']);
  	}
  	echo json_encode($items);
  }
  
  public function actionAjaxView(){
  	header("Content-type: application/json;charset=utf-8");
  	$sql = "select id, exception as content from {exceptions} where id=:id";
  	$item = TCClick::app()->db->query($sql, array(":id"=>$_GET['id']))->fetch(PDO::FETCH_ASSOC);
  	echo json_encode($item);
  }
  
  public function actionAjaxSetLocatedContent(){
  	$sql = "update {exceptions} set exception=:exception, located=1 where id=:id";
  	TCClick::app()->db->execute($sql, array(":exception"=>$_POST['content'], ":id"=>$_GET['id']));
  }
  
  public function actionUpdate(){
  	if($_POST['status'] && $_GET['id']){
  		$sql = "update {exceptions} set status=:status where id=:id";
  		TCClick::app()->db->execute($sql, array(":status"=>intval($_POST['status']), ":id"=>$_GET['id']));
  	}
  	header("Location: ".$_SERVER['HTTP_REFERER']);
  }
  
  public function actionAjaxExceptionsListBlock(){
  	$items_per_page = 20;
  	$status = $_GET['status'] ? intval($_GET['status']) : 0;
  	$condition = "status={$status} ";
  	if(is_numeric($_GET['version_id'])) $condition .= " and version_id={$_GET['version_id']}";
  	
  	$count_sql = "select count(*) from {exceptions} where $condition ";
  	$count = TCClick::app()->db->query($count_sql)->fetchColumn(0);
  	if($count%$items_per_page==0){
  		$pages_count = $count==0 ? 0 : intval($count/$items_per_page);
  	}else{
  		$pages_count = intval($count/$items_per_page)+1;
  	}
  	
  	$current_page = $_GET['page'] ? intval($_GET['page']) : 1;
  	$offset = ($current_page-1) * $items_per_page;
  	$sql = "select * from {exceptions} where $condition ";
  	$sql .= " order by updated_at desc limit $offset, $items_per_page";
  	$rows = TCClick::app()->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  	
  	$params = array("rows"=>$rows, "current_page"=>$current_page, "pages_count"=>$pages_count);
  	$this->renderPartial('ajax_block_exceptions_list', $params);
  }
  
  public function actionAjaxDailyCount(){
  	header("Content-type: application/json;charset=utf-8");
		$start_date = date("Y-m-d", time()-86400*30);
		if(!empty($_GET['start_date'])) $start_date = date('Y-m-d', strtotime($_GET['start_date']));
		$end_date = date("Y-m-d");
		if(!empty($_GET['end_date'])) $end_date = date('Y-m-d', strtotime($_GET['end_date']));
  	$version_id = intval($_GET['version_id']);
  	$json = array("stats"=>array(), "result"=>"success");
  	$daily_count_with_dates = ReportsController::generateZeroDailyCount($start_date, $end_date);
  	$sql = "select `date`, `count` from {counter_exceptions} where `date`>=:start and `date`<=:end
  	and version_id={$version_id}";
  	$stmt = TCClick::app()->db->query($sql, array(":start"=>$start_date, ":end"=>$end_date));
  	foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
  		$daily_count_with_dates[$row['date']] = intval($row['count']);
  	}
  	foreach($daily_count_with_dates as $date=>$count){
  		$daily_count[] = $count;
  	}
  	$json['stats'][] = array("data"=>$daily_count, "name"=>"错误次数");
  	$json['dates'] = ReportsController::datesArrayForJsonOutput($start_date, $end_date);
  	echo json_encode($json);
  }
}

