<?php

require_once dirname(dirname(__FILE__)) . '/init.php';
require_once 'TCClickTestCase.php';

function require_all_files_under_folder($folder){
	if(is_dir($folder)){
		$handle = opendir($folder);
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != ".."){
				$filepath = $folder . $entry;
				if(file_exists($filepath)){
					require_once $filepath;
				}
			}
		}
		closedir($handle);
	}
}

require_once TCClick::app()->root_path . '/protected/analyze/Analyzer.php';
require_all_files_under_folder(TCClick::app()->root_path . '/protected/components/');
require_all_files_under_folder(TCClick::app()->root_path . '/protected/models/');

