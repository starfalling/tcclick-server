<?php

/**
 * 按天计算 Google Play 导流的子渠道新增、活跃情况
 */

include_once dirname(dirname(__FILE__)) . '/protected/init.php';
include_once dirname(dirname(__FILE__)) . '/protected/components/RegPattern.php';
require dirname(__FILE__) . '/_init_with_params.php';


function calculateCountWithAndroidInfo($id_channels, &$count_sites, &$count_campaigns) {
  $sql = "select id, campaign_id, site_id from {devices_android_info} 
            where id in (" . join(',', array_keys($id_channels)) . ")";
  $stmt = TCClick::app()->db->query($sql);
  while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $channel_id = $id_channels[$id];
    $campaign_id = intval($row['campaign_id']);
    $site_id = intval($row['site_id']);
    if(!isset($count_sites[$channel_id])) {
      $count_sites[$channel_id] = array();
    }
    if($site_id > 0) {
      if(!isset($count_sites[$channel_id][$site_id])) {
        $count_sites[$channel_id][$site_id] = 0;
      }
      $count_sites[$channel_id][$site_id]++;
    }
    if(!isset($count_campaigns[$channel_id])) {
      $count_campaigns[$channel_id] = array();
    }
    if($campaign_id > 0) {
      if(!isset($count_campaigns[$channel_id][$campaign_id])) {
        $count_campaigns[$channel_id][$campaign_id] = 0;
      }
      $count_campaigns[$channel_id][$campaign_id]++;
    }
  }
}


function insertCountsWithAndroidInfo($table_name, $field_name, $date, $counts) {
  $values = array();
  foreach($counts as $channel_id => $temp) {
    foreach($temp as $site_id => $count) {
      $values[] = "('{$date}', {$channel_id}, {$site_id}, {$count})";
    }
  }
  if(!empty($values)) {
    $sql = "delete from {{$table_name}} where `date`='{$date}'";
    TCClick::app()->db->execute($sql);

    $sql = "insert ignore into {{$table_name}}
          (`date`, `channel_id`, `{$field_name}`, `count`) values " . join(",", $values);
    TCClick::app()->db->execute($sql);
  }
}

function loadDeviceChannels($ids) {
  $id_channels = array();
  $sql = "select id, channel_id from {devices} where id in (" . join(',', $ids) . ")";
  $stmt = TCClick::app()->db->query($sql);
  while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id_channels[$row['id']] = $row['channel_id'];
  }

  return $id_channels;
}


// 计算不同广告位、siteid 的新增情况
$sql = "select id, channel_id from {devices} 
        where created_at>='$date' and created_at<='$date 23:59:59'";
$new_count_sites = array();
$new_count_campaigns = array();
$id_channels = array();
$stmt = TCClick::app()->db->query($sql);
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $id_channels[$row['id']] = $row['channel_id'];
  // 批量查询设备的导流信息
  if(count($id_channels) >= 1) {
    calculateCountWithAndroidInfo($id_channels, $new_count_sites, $new_count_campaigns);
    $id_channels = array();
  }
}
if(!empty($id_channels)) {
  calculateCountWithAndroidInfo($id_channels, $new_count_sites, $new_count_campaigns);
  $id_channels = array();
}

// 数据记录插入数据库
insertCountsWithAndroidInfo('counter_daily_new_with_android_info_site_id', 'site_id', $date, $new_count_sites);
insertCountsWithAndroidInfo('counter_daily_new_with_android_info_campaign_id', 'campaign_id', $date, $new_count_campaigns);


// 计算活跃情况
$active_count_sites = array();
$active_count_campaigns = array();
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
      $id_channels = loadDeviceChannels($ids);
      calculateCountWithAndroidInfo($id_channels, $active_count_sites, $active_count_campaigns);
      $ids = array();
    }
  }
  gzclose($handle);
  if(!empty($ids)) {
    $id_channels = loadDeviceChannels($ids);
    calculateCountWithAndroidInfo($id_channels, $active_count_sites, $active_count_campaigns);
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
      $ids[] = $row["device_id"];
    }
    if(empty($ids)) break;
    $id_channels = loadDeviceChannels($ids);
    calculateCountWithAndroidInfo($id_channels, $active_count_sites, $active_count_campaigns);
    if(count($ids) != $row_limit_per_fetch) break;
  }

}


// 数据记录插入数据库
insertCountsWithAndroidInfo('counter_daily_active_with_android_info_site_id', 'site_id', $date, $active_count_sites);
insertCountsWithAndroidInfo('counter_daily_active_with_android_info_campaign_id', 'campaign_id', $date, $active_count_campaigns);


// 重新计算子渠道 site id 的总用户数
$total_counts = array();
$sql = "select channel_id, site_id, `count` from {counter_daily_new_with_android_info_site_id}";
$stmt = TCClick::app()->db->query($sql);
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $channel_id = intval($row['channel_id']);
  $site_id = intval($row['site_id']);
  $count = intval($row['count']);
  if(!isset($total_counts[$channel_id])) {
    $total_counts[$channel_id] = null;
  }
  if(!isset($total_counts[$channel_id][$site_id])) {
    $total_counts[$channel_id][$site_id] = 0;
  }
  $total_counts[$channel_id][$site_id] += $count;
}
foreach($total_counts as $channel_id => &$data) {
  arsort($data);
}
Config::set(Config::KEY_DEVICE_COUNTS_WITH_ANDROID_INFO_SITE_ID, $total_counts);


// 重新计算子渠道 campaign id 的总用户数
$total_counts = array();
$sql = "select channel_id, campaign_id, `count` from {counter_daily_new_with_android_info_campaign_id}";
$stmt = TCClick::app()->db->query($sql);
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $channel_id = intval($row['channel_id']);
  $campaign_id = intval($row['campaign_id']);
  $count = intval($row['count']);
  if(!isset($total_counts[$channel_id])) {
    $total_counts[$channel_id] = null;
  }
  if(!isset($total_counts[$channel_id][$campaign_id])) {
    $total_counts[$channel_id][$campaign_id] = 0;
  }
  $total_counts[$channel_id][$campaign_id] += $count;
}
foreach($total_counts as $channel_id => &$data) {
  arsort($data);
}
Config::set(Config::KEY_DEVICE_COUNTS_WITH_ANDROID_INFO_CAMPAIGN_ID, $total_counts);

