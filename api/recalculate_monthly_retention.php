<?php
/**
 * 计算月留存
 * @var string $date
 */

include_once dirname(dirname(__FILE__)) . '/protected/init.php';
include_once dirname(dirname(__FILE__)) . '/protected/components/RegPattern.php';
require dirname(__FILE__) . '/_init_with_params.php';
$month_start_date = substr($date, 0, 7) . '-01';
$month_start_time = strtotime($month_start_date);
$month_active_table_name = "monthly_active_devices_" . date('Y_m', strtotime($date));


// 计算之前八个月的留存数据
$prev_month_start_time = $month_start_time;
for($i = 1; $i <= 8; $i++) {
  $this_month_end_date = date('Y-m-d H:i:s', $prev_month_start_time - 1);
  $this_month_start_date = substr($this_month_end_date, 0, 7) . '-01';
  $this_month_start_time = strtotime($this_month_start_date);
  $prev_month_start_time = $this_month_start_time;

  $start_date = $this_month_start_date;
  $end_date = $this_month_end_date;

  // 查询出这一个月的新增用户的最小、最大 ID
  $sql = "select min(id) as `min`, max(id) as `max` 
          from {devices} 
          where created_at>=:start and created_at<=:end";
  $params = array(':start' => $start_date, ':end' => $end_date);
  $row = TCClick::app()->db->query($sql, $params)->fetch(PDO::FETCH_ASSOC);
  if(empty($row) || empty($row['min']) || empty($row['max'])) continue;

  $min_id = intval($row['min']);
  $max_id = intval($row['max']) + 1;
//  echo $min_id, "\t", $max_id, "\n";
//  var_dump($params);
  $new_counts = array($max_id - $min_id);
  $retention_counts = array(0);

  // 按照一千个一组进行分析
  $step = 1000;
  for(; $min_id < $max_id; $min_id += $step) {
    $max = $min_id + $step;
    if($max > $max_id) $max = $max_id;

    // 查询出有哪些设备 ID 在本月活跃过
    $active_device_ids = array();
    $sql = "select device_id from {{$month_active_table_name}} 
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
    $sql = "insert into {retention_rate_monthly} 
      (`date`, new_count, channel_id, retention{$i}) values " .
      join(',', $retention_counts_sql) .
      " on duplicate key update retention{$i}=values(retention{$i}), new_count=values(new_count)";
    TCClick::app()->db->execute($sql);
  }
}


