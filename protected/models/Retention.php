<?php
class Retention{
  // 每周新增设备
  public static function weeklyNewDevicecounter($i){
    $start_date = date('Y-m-d', time() - 86400 * $i);
    $to_date = date('Y-m-d', time() - 86400 * ($i - 7));
    $devices_id = array();
    $sql = "select id  from {devices} where created_at>='$start_date 00:00:00' and created_at <'$to_date 00:00:00'";
    $stmt = TCClick::app()->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    $count = 0;
    foreach($stmt as $row){
      $count += 1;
      $devices_id[] = $row['id'];
    }
    $device_count = $count;
    return array('count' => $device_count, 'id' => $devices_id);
  }
  // 周的留存率
  public static function weeklyRetentionRate($i, $weekly, $new_device_count){
    $date = date('Y-m-d', (time() - 86400 * $i + 86400 * 7 * $weekly));
    $table_name = str_replace("-", "_", "{weekly_active_devices_$date}");
    $sql = "select device_id from $table_name";
    $stmt = TCClick::app()->db->query($sql);
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){$device_id[] = $row['device_id'];
    }
    $same_device_id_count = count(array_intersect($new_device_count['id'], $device_id));
    $weekly_retention_rate = round($same_device_id_count / $new_device_count['count'] * 100, 2);
    return $weekly_retention_rate;
  }
  // 月的新增设备
  public static function monthlyDeviceCount($i){
    $j = $i - 1;
    $month = date('Y-m-01', strtotime("-$i month"));
    $to_month = date('Y-m-01', strtotime("-$j month"));
    $sql = "select id from {devices} where created_at>='$month 00:00:00' and created_at <'$to_month 00:00:00'";
    $stmt = TCClick::app()->db->query($sql);
    $count = 0;
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
      $count += 1;
      $device_id[] = $row['id'];
    }
    $device_count = $count;
    return array('count' => $device_count, 'id' => $device_id);
  }
  // 月留存率
  public static function monthlyRetentionRate($i, $monthly, $new_device_count){
    $j = $i - $monthly;
    $moth = date('Y-m', strtotime("-$j month"));
    $table_name = str_replace("-", "_", "{monthly_active_devices_$moth}");
    $sql = "select device_id from $table_name";
    $stmt = TCClick::app()->db->query($sql);
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
      $device_id[] = $row['device_id'];
    }
    $same_device_id_count = count(array_intersect($new_device_count['id'], $device_id));
    $monthly_retention_rate = round($same_device_id_count / $new_device_count['count'] * 100, 2);
    return $monthly_retention_rate;
  }
}