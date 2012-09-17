<?php

include_once "components/TCClick.php";
include_once "config.php";


function __autoload($classname) {
	$base_path = dirname(__FILE__);
	if (file_exists($base_path . "/models/{$classname}.php")){
		include_once $base_path . "/models/{$classname}.php";
	}
	if (file_exists($base_path . "/components/{$classname}.php")){
		include_once $base_path . "/components/{$classname}.php";
	}
}

