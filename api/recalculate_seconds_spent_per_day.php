<?php
include_once dirname(dirname(__FILE__)) . '/protected/init.php';
include_once dirname(dirname(__FILE__)) . '/protected/components/RegPattern.php';
require dirname(__FILE__) . '/_init_with_params.php';

// 重新计算某一天各个使用时长区间的设备数分布
$sql = "delete from {counter_daily_seconds_spent_per_day} where date=:date";
TCClick::app()->db->execute($sql, array(":date"=>$date));

$date_for_tablename = str_replace("-", "_", $date);
$tablename = "daily_active_devices_{$date_for_tablename}";
$sql = "select (case
	when seconds_spent<=3 then 1
	when seconds_spent<=10 then 2
	when seconds_spent<=30 then 3
	when seconds_spent<=60 then 4
	when seconds_spent<=180 then 5
	when seconds_spent<=600 then 6
	when seconds_spent<=1800 then 7
	else 8 end
) as seconds_spent_id,
count(*) as `count`
from {{$tablename}}
where seconds_spent>0
group by seconds_spent_id";
$stmt = TCClick::app()->db->query($sql);
$sql = "insert ignore into {counter_daily_seconds_spent_per_day}
(date, seconds_spent_id, count) values ";
foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
	$sql .= "(:date, {$row['seconds_spent_id']}, {$row['count']}),";
}
$sql = substr($sql, 0, strlen($sql)-1);
TCClick::app()->db->execute($sql, array(":date"=>$date));
