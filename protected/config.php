<?php

define("IS_ANDROID", 1);
define("TCCLICK_DEBUG", 1);
define("TCCLICK_DEBUG_SQL_STATISTICS", 0); // 是否对sql执行情况进行统计

if(defined('SAE_TMP_PATH')) include 'config_sae.php';

define("MYSQL_DSN_MASTER", "mysql:host=127.0.0.1;port=3306;dbname=tcclick;charset=utf8");
define("MYSQL_USER_MASTER", "root");
define("MYSQL_PASS_MASTER", "root");

define("MYSQL_DSN_SLAVE", MYSQL_DSN_MASTER);
define("MYSQL_USER_SLAVE", MYSQL_USER_MASTER);
define("MYSQL_PASS_SLAVE", MYSQL_PASS_MASTER);

define("MYSQL_PERSISTENT", true);

define("MYSQL_TABLE_PREFIX", "tcclick_");


define("MEMCACHE_KEY_PREFIX", "");
define("MEMCACHE_HOST", "localhost");
define("MEMCACHE_PORT", 12006);

// 小时活跃设备列表的表存在时间，也就是 hourly_active_devices_2012_08_01 这样的表会在下面配置的时间之后由cron任务删除
define("TCCLICK_HOURLY_ACTIVE_DEVICE_RECORD_TIME", 86400*3);
// 日活跃设备列表的表存在时间，也就是 daily_active_devices_2012_08_01 这样的表会在下面配置的时间之后由cron任务删除
define("TCCLICK_DAILY_ACTIVE_DEVICE_RECORD_TIME", 86400*14);

define("SAE_CLIENT_ACTIVITY_FORWARD_URL", false);
define("SAE_ANALYZE_CLOSED", false);

global $router_config;
$router_config = array(
		'/'=>'site/index',
		'login'=>'site/login',
		'logout'=>'site/logout',
		'<controller:\w+>/?'=>'<controller>/index',
		'<controller:\w+>/<id:\d+>'=>'<controller>/view',
		'<controller:\w+>/<id:\d+>/<action:\w+>'=>'<controller>/<action>',
		'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
);
