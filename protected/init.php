<?php

include "components/TCClick.php";
include "config.php";


function __autoload($classname) {
	$base_path = dirname(__FILE__);
	if (file_exists($base_path . "/models/{$classname}.php")){
		include $base_path . "/models/{$classname}.php";
	}
	if (file_exists($base_path . "/components/{$classname}.php")){
		include $base_path . "/components/{$classname}.php";
	}
}

