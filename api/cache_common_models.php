<?php
include dirname(dirname(__FILE__)) . '/protected/init.php';

/**
 * 把昨天活跃的设备中，活跃用户数最多的一万种设备的设备号与名字的映射关系存储到memcache当中，以降低sql查询次数
 * 
 */

$cache_key = "tcclick_cached_device_models";
$data = array();
$yesterday = date('Y-m-d', time()-86400);
$sql = "select sum(count) c, model_id from tcclick_counter_daily_active_model
where date='{$yesterday}' group by model_id order by c desc limit 200";
$stmt = TCClick::app()->db->query($sql);
while(($row=$stmt->fetch(PDO::FETCH_ASSOC)) != null){
	$model_name = Model::nameof($row['model_id']);
	$data[$model_name] = $row['model_id'];
}
TCClick::app()->cache->set($cache_key, $data);
