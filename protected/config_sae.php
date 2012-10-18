<?php


define("MYSQL_DSN_MASTER", "mysql:host=".SAE_MYSQL_HOST_M.";port=".SAE_MYSQL_PORT.";dbname=".SAE_MYSQL_DB.";charset=utf8");
define("MYSQL_DSN_SLAVE", "mysql:host=".SAE_MYSQL_HOST_S.";port=".SAE_MYSQL_PORT.";dbname=".SAE_MYSQL_DB.";charset=utf8");

define("MYSQL_USER_MASTER", SAE_MYSQL_USER);
define("MYSQL_PASS_MASTER", SAE_MYSQL_PASS);

define("MYSQL_USER_SLAVE", SAE_MYSQL_USER);
define("MYSQL_PASS_SLAVE", SAE_MYSQL_PASS);

define("STORAGE_DOMAIN_EXPORTED_DEVICE_IDS", "deviceids");

ini_set("display_errors", false);
define("MYSQL_PERSISTENT", false); // SAE下使用持久连接会造成连接数超限
