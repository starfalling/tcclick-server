<?php
include_once dirname(dirname(__FILE__)) . '/protected/init.php';
include_once TCClick::app()->root_path . '/protected/components/RegPattern.php';

$date = date("Y-m-d");
if($_GET['date'] && preg_match(RegPattern::DATE, $_GET['date'])){
	$date = $_GET['date'];
}
if($_GET['date'] == "yesterday") $date = date("Y-m-d", time()-86400);

$tablename = "daily_active_devices_" . str_replace("-", "_", $date);
$tmp_filepath = SAE_TMP_PATH . '/' . $tablename . '.txt';
$handle = fopen($tmp_filepath, 'w');

$min_device_id = 0;
$row_limit_per_fetch = 500000;
while(true){
	$sql = "select device_id from {{$tablename}} where device_id>{$min_device_id} 
					order by device_id limit $row_limit_per_fetch";
	$row_count = 0;
	$stmt = TCClick::app()->db->query($sql);
	while(($row = $stmt->fetch(PDO::FETCH_ASSOC)) != null){
		fwrite($handle, pack('I', $row["device_id"]), 4);
		$row_count++;
		if ($min_device_id<$row["device_id"]) $min_device_id=$row["device_id"];
	}
	if ($row_count != $row_limit_per_fetch) break;
}


fclose($handle);

$s = new SaeStorage();
$s->upload(STORAGE_DOMAIN_EXPORTED_DEVICE_IDS, $tablename.'.txt.gz', $tmp_filepath, array(), true);
