<?php

$handle = fopen(SAE_TMP_PATH."/test.txt", "w");
for($i=0; $i<1024 * 1024 * 10; $i++){
	fwrite($handle, "0123456789");
}

echo "memory usage: ", memory_get_usage(), "\n";
echo "memory usage real: ", memory_get_usage(true), "\n";

