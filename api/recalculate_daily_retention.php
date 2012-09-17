<?php
include_once dirname(dirname(__FILE__)) . '/protected/init.php';
include_once TCClick::app()->root_path . '/protected/components/RegPattern.php';

$date = date("Y-m-d");
if($_GET['date'] && preg_match(RegPattern::DATE, $_GET['date'])){
	$date = $_GET['date'];
}
if($_GET['date'] == "yesterday") $date = date("Y-m-d", time()-86400);
$time = strtotime($date);


// 从 SaeStorage 读取记录活跃设备号的备份文件，然后执行解压缩，放如临时文件当中
$gz_filename = "daily_active_devices_" . str_replace("-", "_", $date) . ".txt.gz";
$s = new SaeStorage();
$tmp_gz_filepath = SAE_TMP_PATH . $gz_filename;
$tmp_filepath = SAE_TMP_PATH . "daily_active_devices_" . str_replace("-", "_", $date) . ".txt";
file_put_contents($tmp_gz_filepath, $s->read(STORAGE_DOMAIN_EXPORTED_DEVICE_IDS, $gz_filename));
$handle = gzopen($tmp_gz_filepath, 'r');
while (!gzeof($handle)) $active_device_ids_str .= gzread($handle, 102400);
gzclose($handle);
file_put_contents($tmp_filepath, $active_device_ids_str); // 把解压缩了的文件存入临时文件系统
$active_device_ids_str = null;


// 计算某一天之前八天的相关留存率数据
for($i=1; $i<=8; $i++){
	$new_date = date("Y-m-d", $time-86400*$i); // 计算这一天的新用户在 $date 时候的留存用户数
	$sql = "select id from {devices} where created_at>='{$new_date}' and created_at<='{$new_date} 23:59:59'
	order by id limit 1";
	$min_device_id = TCClick::app()->db->query($sql)->fetchColumn();
// 	echo 'min_device_id:', $min_device_id, "\n";
	if (!$min_device_id) continue; // 没有新增用户
	$sql = "select id from {devices} where created_at>='{$new_date}' and created_at<='{$new_date} 23:59:59'
	order by id desc limit 1";
	$max_device_id = TCClick::app()->db->query($sql)->fetchColumn();
// 	echo 'max_device_id:', $max_device_id, "\n";
	
	$min_index = TCClickUtil::seekIndexThatFirstGreaterOrEqualInFile($min_device_id, $tmp_filepath);
	if ($min_index === false) continue; // 没有找到
// 	echo 'min_index:', $min_index, "\n";
	
	// 取出所有的活跃设备的ID，放在一个哈希数组当中
	$handle = fopen($tmp_filepath, 'r');
	fseek($handle, $min_index*4, SEEK_CUR);
	$active_device_ids = array(); // 在这一天新增设备范围之内的活跃设备号
	while(true){
		$device_id = unpack('I', fread($handle, 4));
		$device_id = $device_id[1];
		if($device_id > $max_device_id) break;
		$active_device_ids[$device_id] = true;
	}
	fclose($handle);
	
	$retention_counts = array(0=>count($active_device_ids)); // 所有渠道的留存
	
	// 计算分渠道的留存情况
	$sql = "select id, channel_id from {devices} where created_at>='{$new_date}' and created_at<='{$new_date} 23:59:59'
	order by id";
	$stmt = TCClick::app()->db->query($sql);
	$new_counts = array();
	while(true){
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!$row) break;
		$new_counts[0]++;
		$new_counts[$row['channel_id']] ++;
		
		if(!$active_device_ids[$row['id']]) continue; // 这个用户这天没有活跃
		if ($retention_counts[$row['channel_id']]) $retention_counts[$row['channel_id']]++;
		else $retention_counts[$row['channel_id']] = 1;
	}
	
	// 存到数据库
	$retention_counts_sql = array();
	foreach($retention_counts as $channel_id=>$count){
		$retention = (int)($count * 10000 / $new_counts[$channel_id]);
		$retention_counts_sql[] = "('{$new_date}', {$new_counts[$channel_id]}, {$channel_id}, $retention)";
	}
	$sql = "insert into {retention_rate_daily} (date, new_count, channel_id, retention{$i}) values ";
	$sql .= join(',', $retention_counts_sql);
	$sql .= " on duplicate key update retention{$i}=values(retention{$i}), new_count=values(new_count)";
	TCClick::app()->db->execute($sql);
}
