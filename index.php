<?php
include 'protected/init.php';
ob_start('ob_gzhandler');


// if(defined('SAE_TMP_PATH')){
// 	sae_xhprof_start();
// }

TCClick::app()->run();

// if(defined('SAE_TMP_PATH')){
// 	sae_xhprof_end();
// }
