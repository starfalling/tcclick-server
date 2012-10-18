<?php

include dirname(dirname(__FILE__)) . '/protected/init.php';
include TCClick::app()->root_path . '/protected/analyze/Analyzer.php';
TCClick::app()->db->connect();

// $handle = fopen(SAE_TMP_PATH."/test.txt", "w");
// for($i=0; $i<1024 * 1024 * 10; $i++){
// 	fwrite($handle, "0123456789");
// }

// echo "memory usage: ", memory_get_usage(), "\n";
// echo "memory usage real: ", memory_get_usage(true), "\n";

var_dump(TCClick::app()->cache->get('tcclick_all_channels'));