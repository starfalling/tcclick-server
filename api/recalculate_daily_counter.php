<?php
/**
 * 重新计算daily_counter的各项值
 * @var string $date
 */

include_once dirname(dirname(__FILE__)) . '/protected/init.php';
include_once dirname(dirname(__FILE__)) . '/protected/components/RegPattern.php';
require dirname(__FILE__) . '/_init_with_params.php';


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

// 找到当天活跃设备里面的最大的设备ID号码
$sql = "select max(device_id) from {{$tablename}}";
$max_device_id = TCClick::app()->db->query($sql)->fetchColumn(0);

$rows_per_fetch = 50000; // 每次获取5万，因为SAE不允许对超过20万行数据执行查询操作
$from = 0;
$to = $rows_per_fetch;
while(true) {
  $sql = "select count(*), sum(open_times), sum(open_times_with_seconds_spent), sum(seconds_spent)
	from {{$tablename}} where device_id>{$from} and device_id<={$to}";
  $row = TCClick::app()->db->query($sql)->fetch(PDO::FETCH_BOTH);
  if($row) {
    $active_devices_count += $row[0];
    $open_times += $row[1];
    $open_times_with_seconds_spent += $row[2];
    $seconds_spent += $row[3];
  }
  $from += $rows_per_fetch;
  $to += $rows_per_fetch;
  if($from > $max_device_id) break;
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
TCClick::app()->db->execute($sql, array(":date" => $date));
