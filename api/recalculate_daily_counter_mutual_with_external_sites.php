<?php
/**
 * 重新计算某一天的外部站共同用户数据
 * @var string $date
 */

include_once dirname(dirname(__FILE__)) . '/protected/init.php';
include_once dirname(dirname(__FILE__)) . '/protected/components/RegPattern.php';
require dirname(__FILE__) . '/_init_with_params.php';


$external_sites = array();
foreach(ExternalSite::all() as $site) {
  if(!$site->calculate_mutual_devices) continue;
  $external_sites[] = $site;
}
if(empty($external_sites)) return;


/**
 * @param array $udids
 * @param ExternalSite[] $external_sites
 * @param array $counts
 */
function loadMutualDevicesCount($udids, $external_sites, &$counts, $date) {
  if(is_int($udids[0])) $udids = Device::loadUdidsById($udids);
  $post_data = '';
  foreach($udids as $udid) {
    $hex = hex2bin($udid);
    if(strlen($hex) !== 16) continue;
    $post_data .= $hex;
  }

  foreach($external_sites as $site) {
    $url = $site->url . 'devices/QueryDevicesExistsCount?external_code=' . $site->code;
    $url .= "&date={$date}";
    $json = null;
    for($i = 0; $i < 5; $i++) {
      // 失败重试五次
      $json = @json_decode(HttpUtil::curl_post($url, $post_data));
      if(!empty($json) && isset($json->count)) break;
    }
    if($json && $json->count) {
      if(!isset($counts[$site->id])) $counts[$site->id] = $json->count;
      else $counts[$site->id] += $json->count;
    }
  }
}


// 新增设备统计
$sql = "select udid from {devices} where created_at>='{$date}' and created_at<='{$date} 23:59:59'";
$stmt = TCClick::app()->db->query($sql);
$udids = array();
$new_counts = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $udids[] = $row['udid'];
  if(count($udids) >= 1000) {
    loadMutualDevicesCount($udids, $external_sites, $new_counts, $date);
    $udids = array();
  }
}
if(!empty($udids)) loadMutualDevicesCount($udids, $external_sites, $new_counts, $date);
if(!empty($new_counts)) {
  // 把新增数据存入数据库
  $sql = "insert into {counter_daily_mutual_with_external_sites}
          (`date`, `external_site_id`, `new_count`) values ";
  $sql_values = array();
  foreach($new_counts as $external_site_id => $count) {
    $sql_values[] = "('{$date}', {$external_site_id}, {$count})";
  }
  $sql .= join(',', $sql_values) . ' on duplicate key update new_count=values(new_count)';
  TCClick::app()->db->execute($sql);
}


// 活跃设备统计
$active_counts = array();
$tablename = "daily_active_devices_" . str_replace("-", "_", $date);
$device_ids_file_path = TCClick::app()->root_path . "/protected/runtime/device_ids/" . $tablename . '.txt.gz';
if(file_exists($device_ids_file_path)) {
  // 从设备文件中拿取活跃设备号
  $ids = array();
  $handle = gzopen($device_ids_file_path, 'r');
  while(true) {
    $str = gzread($handle, 4);
    if(empty($str)) break;
    $device_id = unpack('I', $str);
    $ids[] = $device_id[1];
    if(count($ids) >= 1000) {
      loadMutualDevicesCount($ids, $external_sites, $active_counts, $date);
      $ids = array();
    }
  }
  gzclose($handle);
  if(!empty($ids)) {
    loadMutualDevicesCount($ids, $external_sites, $active_counts, $date);
    $ids = array();
  }
} else {
  // 从数据库中查询活跃设备
  $min_device_id = 0;
  $row_limit_per_fetch = 1000;
  while(true) {
    $ids = array();
    $sql = "select device_id from {{$tablename}} where device_id>{$min_device_id} 
					order by device_id limit $row_limit_per_fetch";
    $stmt = TCClick::app()->db->query($sql);
    while(($row = $stmt->fetch(PDO::FETCH_ASSOC)) != null) {
      $ids[] = intval($row["device_id"]);
    }
    $min_device_id = $ids[count($ids) - 1];
    if(empty($ids)) break;
    loadMutualDevicesCount($ids, $external_sites, $active_counts, $date);
    if(count($ids) != $row_limit_per_fetch) break;
  }
}


if(!empty($active_counts)) {
  // 把活跃数据存入数据库
  $sql = "insert into {counter_daily_mutual_with_external_sites}
          (`date`, `external_site_id`, `active_count`) values ";
  $sql_values = array();
  foreach($active_counts as $external_site_id => $count) {
    $sql_values[] = "('{$date}', {$external_site_id}, {$count})";
  }
  $sql .= join(',', $sql_values) . ' on duplicate key update active_count=values(active_count)';
  TCClick::app()->db->execute($sql);
}
