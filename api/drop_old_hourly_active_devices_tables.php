<?php

/**
 * 删除过早的用来存储小时活跃设备ID列表的table，以释放数据库空间
 */

include dirname(dirname(__FILE__)) . '/protected/init.php';
include_once dirname(dirname(__FILE__)) . '/protected/components/RegPattern.php';


$today = strtotime(date('Y-m-d'));
$from_time = $today-TCCLICK_HOURLY_ACTIVE_DEVICE_RECORD_TIME-86400;
$to_time = $today-TCCLICK_HOURLY_ACTIVE_DEVICE_RECORD_TIME;
if(isset($_GET['from']) && preg_match(RegPattern::DATE, $_GET['from'])){
	$time = strtotime($_GET['from']);
	if($time < $from_time) $from_time = $time;
}

// 逐天删除这些表
for($time=$from_time; $time<$to_time; $time+=86400){
	$table_name = "hourly_active_devices_" . date('Y_m_d', $time);
	$sql = "drop table {{$table_name}}";
	echo $sql, "\n";
	TCClick::app()->db->execute($sql);
}


