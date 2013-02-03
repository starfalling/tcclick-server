<?php
class ReportsAreasController extends  Controller{
	public function filters(){
		return array("AdminRequiredFilter");
	}
	
	
  public function actionIndex(){
    $this->render('index');
  }
  
  public function actionAjaxBlackTopTenCuntAreaDevices(){
    header("Content-type: application/json;charset=utf-8");
    $today = date('Y-m-d', time());
    $proprotion_devices = array();
    $names = array();
    $json = array("status"=>array(), "datas"=>array(), "result"=>"success");
    $sql = "select sum(count) as total from {counter_daily_new_area} where area_id<=35";
    $device_total_count = TCClick::app()->db->query($sql)->fetchColumn(); //中国境内所有设备
    $sql = "select sum(count) as total, area_id from {counter_daily_new_area} where   area_id<=35 and area_id >1  GROUP BY area_id  ORDER BY total DESC LIMIT 10";
    $stmt = TCClick::app()->db->query($sql);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
      $proprotion_devices[$row['area_id']] = round($row['total']/$device_total_count,4)*100;//中国境内设备数量排名前十的的省份 比例
      $names[$row['area_id']] = Area::nameof($row['area_id']);
      $json['status'][] = array('name'=>Area::nameof($row['area_id']), 'proprotion'=>round($row['total']/$device_total_count,4)*100);
    }
    $json['datas'] = array('column1'=>'省市', 'column2'=>'用户比例');
    echo json_encode($json);
  }
}