<?php

global $dysm_files, $app_names, $root_url;
$dysm_files = array(
		"p0.9" => "/Users/york/Library/Developer/Xcode/Archives/2012-09-05/"
		."KankanIpad 9-5-12 9.20 PM.xcarchive/dSYMs/KankanIpad.app.dSYM/"
		."Contents/Resources/DWARF/KankanIpad"
);
$root_url = "http://ipad.yingshi.tcclick.1kxun.com";
$root_url = "http://yingshiipadtcclick.sinaapp.com";


// 获取执行程序的名字
foreach($dysm_files as $version=>$path){
	preg_match('|[^/]+$|', $path, $matches);
	$app_names[$version] = $matches[0];
}


$url = $root_url . "/exceptions/AjaxListUnLocated";
$json = json_decode(file_get_contents($url));
if($json){
	foreach($json as $item){
		locate_exception($item->id, $item->version);
		echo "exception located: ", $item->id, "\n";
	}
}




function locate_exception($id, $version){
	global $dysm_files, $app_names, $root_url;
	$url = $root_url . "/exceptions/{$id}/AjaxView";
	$json = json_decode(file_get_contents($url));
	if(!$json) return;
	$origin_content = $json->content;
	$app_name = $app_names[$version];
	$new_content = $origin_content;
	$reg = "|{$app_name}( *)(0x[0-9a-z]{8}) {$app_name} \\+ [0-9]+|";
	if(preg_match_all($reg, $origin_content, $matches)){
		foreach($matches[2] as $i=>$address){
			$command = "atos -o '{$dysm_files[$version]}' -arch armv7 $address";
			$output = exec($command);
			$new_content = str_replace($matches[0][$i], 
					"{$app_name}{$matches[1][$i]}{$address} {$output}", $new_content);
		}
	}
	
	// 上传
	include_once dirname(dirname(__FILE__)) . '/components/HttpUtil.php';
	$url = $root_url . "/exceptions/{$id}/AjaxSetLocatedContent";
	HttpUtil::curl_post($url, array("content"=>$new_content));
}

