<?php


class ExternalAccessFilter extends Filter{
	public function preFilter(){
		if($_GET['external_site_id']){
			$site = ExternalSite::findById($_GET['external_site_id']);
			if($site){
				$root_url = $site->url;
				if($root_url[strlen($root_url)-1]=='/') $root_url = substr($root_url, 0, strlen($root_url)-1);
				$request_uri = $_SERVER['REQUEST_URI'];
				$request_uri = preg_replace('/external_site_id=[^&]+&?/', '', $request_uri);
				if($request_uri[strlen($request_uri)-1]=='?' || $request_uri[strlen($request_uri)-1]=='&'){
					$url = $root_url . $request_uri . "external_code=" . $site->code;
				}else{
					$url = $root_url . $request_uri . "&external_code=" . $site->code;
				}
				header("Content-type: application/json;charset=utf-8");
				$json = json_decode(HttpUtil::curl_get($url));
				if($json) echo json_encode($json);
				else echo "{}";
			}
			exit;
		}
		return true;
	}
	
}

