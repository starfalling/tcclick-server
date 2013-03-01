<?php
/**
 * 根据昨天的统计状况，缓存一些模型数据以降低在进行数据分析时的sql查询次数
 */

include dirname(dirname(__FILE__)) . '/protected/init.php';
$yesterday = date('Y-m-d', time()-86400);

/**
 * 把昨天活跃的设备中，活跃用户数最多的500种设备的设备号与名字的映射关系存储到memcache当中，以降低sql查询次数
 */
$cache_key = "tcclick_cached_device_models";
$data = array();
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
$sql = "select sum(count) c, resolution_id from {counter_daily_active_resolution}
where date='{$yesterday}' group by resolution_id order by c desc limit 500";
$stmt = TCClick::app()->db->query($sql);
while(($row=$stmt->fetch(PDO::FETCH_ASSOC)) != null){
	$resolution_name = Resolution::nameof($row['resolution_id']);
	$data[$resolution_name] = $row['resolution_id'];
}
TCClick::app()->cache->set($cache_key, $data);
// var_dump(TCClick::app()->cache->get($cache_key));


/**
 * 把昨天活跃的设备中，发生次数最多的前500个客户端事件存储到memcache中，以降低sql查询次数
 * 缓存下来的数据结构为：
 * event_id => {
 *   param_name_id => {
 *     # cached data of param row
 *   }, ...
 * }
 */
$cache_key = "tcclick_cached_event_params";
$data = array();
$sql = "select event_id, param_id, sum(count) as c
from {counter_daily_events} where date='{$yesterday}'
group by event_id, param_id
order by c desc limit 500";
$should_cache_event_params = array();
$stmt = TCClick::app()->db->query($sql);
while(($row=$stmt->fetch(PDO::FETCH_ASSOC)) != null){
	$should_cache_event_params[$row['event_id']][] = $row['param_id'];
}
foreach($should_cache_event_params as $event_id=>$param_ids){
	$param_data_of_this_event = array();
	$sql = "select * from {event_params} where event_id={$event_id}";
	$stmt = TCClick::app()->db->query($sql);
	while(($row=$stmt->fetch(PDO::FETCH_ASSOC)) != null){
		$param_data_of_this_event[$row['param_id']] = $row;
	}
	foreach($param_ids as $param_id){
		$name_id = $param_data_of_this_event[$param_id]['name_id'];
		$data[$event_id][$name_id] = $param_data_of_this_event[$param_id];
	}
	unset($param_data_of_this_event);
}
TCClick::app()->cache->set($cache_key, $data);
// var_dump(TCClick::app()->cache->get($cache_key));

