<?php

include_once dirname(dirname(__FILE__)) . '/protected/init.php';
include_once TCClick::app()->root_path . '/protected/components/IpLocationSeekerTCClick.php';

$data_compressed = file_get_contents('php://input');
if($data_compressed){
	$sql = "insert into {client_activities} (server_timestamp, data_compressed, ip) 
			values (" . time() . ", :data, :ip)";
	TCClick::app()->db->execute($sql, array(":data"=>$data_compressed, 
	":ip"=>IpLocationSeekerTCClick::ip2int($_SERVER['REMOTE_ADDR'])));
	TCClick::app()->db->close();
}


if(defined('SAE_TMP_PATH')){ // for sae
	$queue = new SaeTaskQueue('tcclick_analyze');
	$queue->addTask("http://{$_SERVER['HTTP_APPNAME']}.sinaapp.com/api/analyze.php");
	$queue->push();
}
