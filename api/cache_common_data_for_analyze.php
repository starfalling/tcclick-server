<?php
/**
 * 根据昨天的统计状况，缓存一些模型数据以降低在进行数据分析时的sql查询次数
 */

include dirname(dirname(__FILE__)) . '/protected/init.php';

/**
 * 把昨天活跃的设备中，活跃用户数最多的500种设备的设备号与名字的映射关系存储到memcache当中，以降低sql查询次数
 */
$cache_key = "tcclick_cached_device_models";
$data = array();
$yesterday = date('Y-m-d', time()-86400);
$sql = "select sum(count) c, model_id from {counter_daily_active_model}
where date='{$yesterday}' group by model_id order by c desc limit 500";
$stmt = TCClick::app()->db->query($sql);
while(($row=$stmt->fetch(PDO::FETCH_ASSOC)) != null){
	$model_name = Model::nameof($row['model_id']);
	$data[$model_name] = $row['model_id'];
}
TCClick::app()->cache->set($cache_key, $data);
// var_dump(TCClick::app()->cache->get($cache_key));


/**
 * 把昨天活跃的设备中，活跃用户数最多的500种设备分辨率名字与ID的映射关系存储到memcache中，以降低sql查询次数
 */
$cache_key = "tcclick_cached_device_resolutions";
$data = array();
$yesterday = date('Y-m-d', time()-86400);
$sql = "select sum(count) c, resolution_id from {counter_daily_active_resolution}
where date='{$yesterday}' group by resolution_id order by c desc limit 500";
$stmt = TCClick::app()->db->query($sql);
while(($row=$stmt->fetch(PDO::FETCH_ASSOC)) != null){
	$resolution_name = Resolution::nameof($row['resolution_id']);
	$data[$resolution_name] = $row['resolution_id'];
}
TCClick::app()->cache->set($cache_key, $data);
// var_dump(TCClick::app()->cache->get($cache_key));




