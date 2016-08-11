<?php
include_once dirname(dirname(__FILE__)) . '/protected/init.php';
include_once dirname(dirname(__FILE__)) . '/protected/components/RegPattern.php';
require dirname(__FILE__) . '/_init_with_params.php';

$tablename = "daily_active_devices_" . str_replace("-", "_", $date);

// SAE 平台下，先不压缩写入到临时文件中，然后在上传至 SaeStorage 的同时执行压缩
// 自部署时，直接写入压缩文件
if(defined('SAE_TMP_PATH')){
	$tmp_filepath = SAE_TMP_PATH . '/' . $tablename . '.txt';
	$handle = fopen($tmp_filepath, 'w');
}else{
	$folder_path = TCClick::app()->root_path . "/protected/runtime/device_ids/";
	if(!is_dir($folder_path)) mkdir($folder_path, 0744);
	$file_path = $folder_path . $tablename . '.txt.gz';
	$handle = gzopen($file_path, 'w');
}

$min_device_id = 0;
$row_limit_per_fetch = 500000;
while(true){
	$sql = "select device_id from {{$tablename}} where device_id>{$min_device_id} 
					order by device_id limit $row_limit_per_fetch";
	$row_count = 0;
	$stmt = TCClick::app()->db->query($sql);
	while(($row = $stmt->fetch(PDO::FETCH_ASSOC)) != null){
		if(defined('SAE_TMP_PATH')){
			fwrite($handle, pack('I', $row["device_id"]), 4);
		}else{
			gzwrite($handle, pack('I', $row["device_id"]), 4);
		}
		$row_count++;
		if ($min_device_id<$row["device_id"]) $min_device_id=$row["device_id"];
	}
	if ($row_count != $row_limit_per_fetch) break;
}


if(defined('SAE_TMP_PATH')){
	$s = new SaeStorage();
	$s->upload(STORAGE_DOMAIN_EXPORTED_DEVICE_IDS, $tablename.'.txt.gz', $tmp_filepath, array(), true);
	fclose($handle);
}else{
	gzclose($handle);
}


