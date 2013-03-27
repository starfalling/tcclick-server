<?php

include_once dirname(dirname(__FILE__)) . '/protected/init.php';
include_once TCClick::app()->root_path . '/protected/components/IpLocationSeekerTCClick.php';

$data_compressed = file_get_contents('php://input');
if($data_compressed){
	$should_insert_activity = true;
	if(defined('SAE_TMP_PATH') && SAE_ANALYZE_CLOSED) $should_insert_activity = false;
	if($should_insert_activity){
		$ip = $_SERVER['REMOTE_ADDR'];
		if(isset($_SERVER['HTTP_REAL_IP'])) $ip = $_SERVER['HTTP_REAL_IP'];
		$sql = "insert into {client_activities} (server_timestamp, data_compressed, ip) 
				values (" . time() . ", :data, :ip)";
		TCClick::app()->db->execute($sql, array(":data"=>$data_compressed, 
		":ip"=>IpLocationSeekerTCClick::ip2int()));
		TCClick::app()->db->close();
	}
	
	// 当需要弃用 SAE 平台时，把接收到的所有数据转发到指定的URL地址上
	if(defined('SAE_TMP_PATH') && SAE_CLIENT_ACTIVITY_FORWARD_URL){
		$header = array("Real-Ip: {$_SERVER['REMOTE_ADDR']}", 'Content-Type: text/plain');
		$ch = curl_init(SAE_CLIENT_ACTIVITY_FORWARD_URL);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_compressed);
		curl_exec($ch);
	}
	
	if(defined('SAE_TMP_PATH') && !SAE_ANALYZE_CLOSED){ // for sae
		$queue = new SaeTaskQueue('tcclick_analyze');
		$queue->addTask("http://{$_SERVER['HTTP_APPNAME']}.sinaapp.com/api/analyze.php");
		$queue->push();
	}
}



