<?php // SAE平台依赖服务自检程序


$errors = array();

$result = TCClick::app()->cache->set('test', 'test', '1');
if(!$result) $errors[] = "无法连接至memcache";

$is_db_created = true;
try{
	TCClick::app()->db->connect();
}catch(PDOException $e) {
	$is_db_created = false;
  $errors[] = "未初始化数据库";
}
if($is_db_created){
	$sql = "select * from {devices} limit 1";
	$stmt = TCClick::app()->db->query($sql);
	if($stmt->errorCode() == "42S02"){
		$errors[] = "未执行tcclick数据库初始化sql";
	}
	$sql = "select * from {events} limit 1";
	$stmt = TCClick::app()->db->query($sql);
	if($stmt->errorCode() == "42S02"){
		$errors[] = "未执行客户端事件相关的初始化sql";
	}
	$sql = "select * from {qqwry} limit 1";
	$stmt = TCClick::app()->db->query($sql);
	if($stmt->errorCode() == "42S02"){
		$errors[] = "未导入纯真IP地理信息位置数据库";
	}
}
TCClick::app()->db->close();


if(!empty($errors)){
	echo "<div class='message'><div class='error'><ul>";
	foreach($errors as $error){
		echo "<li>{$error}</li>";
	}
	echo "</ul>";
	
	echo "tcclick在Linux上的安装部署步骤参见博文：";
	echo "<a href='http://blog.yorkgu.me/2012/09/29/install_tcclick_on_sina_app_engine/' target='_blank'>",
	"http://blog.yorkgu.me/2012/09/29/install_tcclick_on_sina_app_engine/",
	"</a>";
	echo "</div></div>";
}