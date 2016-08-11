<?php

include dirname(dirname(__FILE__)) . '/protected/init.php';
include dirname(dirname(__FILE__)) . '/protected/analyze/Analyzer.php';

$dbMigrateUtil = new DbMigrateUtil();
$dbMigrateUtil->upgrade();

define("KEY_LOADING_CLIENT_ACTIVIES_LOCK", "KEY_LOADING_CLIENT_ACTIVIES_LOCK");

$script_start_time = time();
while(true) {
  $result = TCClick::app()->cache->add(KEY_LOADING_CLIENT_ACTIVIES_LOCK, "locked", 5);
  if($result) break;
  if(time() - $script_start_time > 1) exit;
  usleep(10 * 1000);
}

$sql = "select * from {client_activities} order by id limit 1000";
$stmt = TCClick::app()->db->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if(!empty($rows)) {
  $max_id = $rows[count($rows) - 1]['id'];
// 	$sql = "insert into {client_activities_backup} select * from {client_activities} where id <= $max_id";
// 	TCClick::app()->db->execute($sql);
  $sql = "delete from {client_activities} where id <= $max_id";
  TCClick::app()->db->execute($sql);
}
TCClick::app()->cache->delete(KEY_LOADING_CLIENT_ACTIVIES_LOCK);

$test_data = '{"timestamp":1470909016, "device":{"udid":"d4d1858a723f478b9c51da392d13bce7",' .
  '"channel":"googlePlay","model":"SCH-I959","brand":"samsung","os_version":"5.0.1","app_version":"2.0",' .
  '"resolution":"1080x1920","locale":"zh_CN","network":"wifi","android_id":"66b1f06fadae3881",' .
  '"referrer":"af_tranid%3DLpqi2as0oa14SfR538neDg%26pid%3D1kxun%26c%3Dtest%26af_siteid%3Dqx",' .
  '"mac":"40:0E:85:7F:F0:E4"}, "data":{"activities":[{"activity":"WelcomeActivity","start_at":1470909013,' .
  '"end_at":1470909014},{"activity":"WelcomeActivity","start_at":1470909014,"end_at":1470909014},' .
  '{"activity":"WelcomeActivity","start_at":1470909014,"end_at":1470909014}],"exceptions":[],"events":[]}}';
$rows = array(array(
  'ip' => '8.8.8.8',
  'server_timestamp' => time(),
  'data_compressed' => gzcompress($test_data),
));

foreach($rows as $row) {
  $data_uncompressed = @gzuncompress($row['data_compressed']);
  if(!$data_uncompressed) $data_uncompressed = @gzinflate($row['data_compressed']);
  if(!$data_uncompressed) continue;

  $analyzer = new Analyzer($row['server_timestamp'], intval($row['ip']), $data_uncompressed);
  $analyzer->analyze();
}