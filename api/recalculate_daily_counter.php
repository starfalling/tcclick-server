<?php
include_once dirname(dirname(__FILE__)) . '/protected/init.php';
include_once TCClick::app()->root_path . '/protected/components/RegPattern.php';

// 重新计算daily_counter的各项值
$date = date("Y-m-d");
if($_GET['date'] && preg_match(RegPattern::DATE, $_GET['date'])){
	$date = $_GET['date'];
}
if($_GET['date'] == "yesterday") $date = date("Y-m-d", time()-86400);

// new devices count
$sql = "select count(*) from {devices} where created_at>='{$date}'
and created_at<='{$date} 23:59:59'";
$stmt = TCClick::app()->db->query($sql);
$new_devices_count = $stmt->fetchColumn(0);
if(!$new_devices_count) $new_devices_count = 0;

// all devices count
$sql = "select count(*) from {devices} where created_at<='{$date} 23:59:59'";
$stmt = TCClick::app()->db->query($sql);
$all_devices_count = $stmt->fetchColumn(0);
if(!$all_devices_count) $all_devices_count = 0;

// update devices count
$update_devices_count = TCClickCounter::calculateUpdateDevicesCountOn($date);

// active devices count
$active_devices_count = $open_times = $open_times_with_seconds_spent = $seconds_spent = 0;
$date_for_tablename = str_replace("-", "_", $date);
$tablename = "daily_active_devices_{$date_for_tablename}";
$sql = "select count(*), sum(open_times), sum(open_times_with_seconds_spent), sum(seconds_spent)
from {{$tablename}}";
$stmt = TCClick::app()->db->query($sql);
$row = $stmt->fetch(PDO::FETCH_BOTH);
if($row){
	$active_devices_count = $row[0];
	$open_times = $row[1];
	$open_times_with_seconds_spent = $row[2];
	$seconds_spent = $row[3];
}

$sql = "insert into {counter_daily}
(`date`, new_devices_count, all_devices_count, active_devices_count, update_devices_count,
open_times, open_times_with_seconds_spent, seconds_spent) values
(:date, {$new_devices_count}, {$all_devices_count}, {$active_devices_count}, {$update_devices_count},
{$open_times}, {$open_times_with_seconds_spent}, {$seconds_spent})
on duplicate key update
new_devices_count={$new_devices_count},
all_devices_count={$all_devices_count},
active_devices_count={$active_devices_count},
update_devices_count={$update_devices_count},
open_times={$open_times},
open_times_with_seconds_spent={$open_times_with_seconds_spent},
seconds_spent={$seconds_spent}";
TCClick::app()->db->execute($sql, array(":date"=>$date));
