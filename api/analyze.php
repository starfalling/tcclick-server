<?php

include dirname(dirname(__FILE__)) . '/protected/init.php';
include dirname(dirname(__FILE__)) . '/protected/analyze/Analyzer.php';

$dbMigrateUtil = new DbMigrateUtil();
$dbMigrateUtil->upgrade();

define("KEY_LOADING_CLIENT_ACTIVIES_LOCK", "KEY_LOADING_CLIENT_ACTIVIES_LOCK");

$script_start_time = time();
while(true){
	$result = TCClick::app()->cache->add(KEY_LOADING_CLIENT_ACTIVIES_LOCK, "locked", 5);
	if($result) break;
	if(time() - $script_start_time > 1) exit;
	usleep(10*1000);
}

$sql = "select * from {client_activities} order by id limit 1000";
$stmt = TCClick::app()->db->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if(!empty($rows)){
	$max_id = $rows[count($rows)-1]['id'];
// 	$sql = "insert into {client_activities_backup} select * from {client_activities} where id <= $max_id";
// 	TCClick::app()->db->execute($sql);
	$sql = "delete from {client_activities} where id <= $max_id";
	TCClick::app()->db->execute($sql);
}
TCClick::app()->cache->delete(KEY_LOADING_CLIENT_ACTIVIES_LOCK);



foreach($rows as $row){
	$data_uncompressed = @gzuncompress($row['data_compressed']);
	if(!$data_uncompressed) $data_uncompressed = @gzinflate($row['data_compressed']);
	if(!$data_uncompressed) continue;

	$analyzer = new Analyzer($row['server_timestamp'], intval($row['ip']), $data_uncompressed);
	$analyzer->analyze();
}