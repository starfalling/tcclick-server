<?php
/**
 * 计算周留存
 * @var string $date
 */

include_once dirname(dirname(__FILE__)) . '/protected/init.php';
include_once dirname(dirname(__FILE__)) . '/protected/components/RegPattern.php';
require dirname(__FILE__) . '/_init_with_params.php';
$week_start_date = TCClickUtil::weekStartDateOf($date);
$week_start_time = strtotime($week_start_date);
$week_active_table_name = "weekly_active_devices_" . date('Y_m_d', $week_start_time);

// 计算之前八周的留存数据
for($i = 1; $i <= 8; $i++) {
  $start_time = $week_start_time - 7 * 86400 * $i;
  $start_date = date('Y-m-d', $start_time);
  $end_time = $start_time + 7 * 86400 - 1;
  $end_date = date('Y-m-d H:i:s', $end_time);

  // 查询出这一周的新增用户的最小、最大 ID
  $sql = "select min(id) as `min`, max(id) as `max` 
          from {devices} 
          where created_at>=:start and created_at<=:end";
  $params = array(':start' => $start_date, ':end' => $end_date);
  $row = TCClick::app()->db->query($sql, $params)->fetch(PDO::FETCH_ASSOC);
  if(empty($row) || empty($row['min']) || empty($row['max'])) continue;

  $min_id = intval($row['min']);
  $max_id = intval($row['max']) + 1;
  $new_counts = array($max_id - $min_id);
  $retention_counts = array(0);

  // 按照一千个一组进行分析
  $step = 1000;
  for(; $min_id < $max_id; $min_id += $step) {
    $max = $min_id + $step;
    if($max > $max_id) $max = $max_id;

    // 查询出有哪些设备 ID 在本周活跃过
    $active_device_ids = array();
    $sql = "select device_id from {{$week_active_table_name}} 
            where device_id>={$min_id} and device_id<{$max}";
    $stmt = TCClick::app()->db->query($sql);
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $active_device_ids[$row['device_id']] = true;
    }

    $sql = "select id, channel_id from {devices} where id>={$min_id} and id<{$max}";
    $stmt = TCClick::app()->db->query($sql);
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $channel_id = $row['channel_id'];
      if(!isset($new_counts[$channel_id])) {
        $new_counts[$channel_id] = 1;
      } else {
        $new_counts[$channel_id] += 1;
      }
      if(!isset($retention_counts[$channel_id])) {
        $retention_counts[$channel_id] = 0;
      }
      if(isset($active_device_ids[$row['id']])) {
        $retention_counts[0] += 1;
        $retention_counts[$channel_id] += 1;
      }
    }
  }

  // 存到数据库
  $retention_counts_sql = array();
  foreach($retention_counts as $channel_id => $count) {
    $retention = (int)($count * 10000 / $new_counts[$channel_id]);
    $retention_counts_sql[] = "('{$start_date}', {$new_counts[$channel_id]}, {$channel_id}, $retention)";
  }
  if(!empty($retention_counts_sql)) {
    $sql = "insert into {retention_rate_weekly} 
      (`date`, new_count, channel_id, retention{$i}) values " .
      join(',', $retention_counts_sql) .
      " on duplicate key update retention{$i}=values(retention{$i}), new_count=values(new_count)";
    TCClick::app()->db->execute($sql);
  }
}


