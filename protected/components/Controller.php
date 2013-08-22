<?php

class Controller{
	private $script_files = array();
	private $style_files = array();
	public $layout = "default";
	public $title = "TCClick";
	public $info;
	public $error;
	
	public function filters(){
		return array();
	}
	
	public function redirect($url){
		header("Location: " . $url);
	}
	
	public function registerCssFile($file){
		$filepath = TCClick::app()->root_path . "/css/$file";
		$modified_time = filemtime($filepath);
		if($modified_time !== false){
			$this->style_files[] = TCClick::app()->root_url . "css/$file?{$modified_time}";
		}
	}
	
	public function registerScriptFile($file){
		$filepath = TCClick::app()->root_path . "/js/$file";
		$modified_time = filemtime($filepath);
		if($modified_time !== false){
			$this->script_files[] = TCClick::app()->root_url . "js/$file?{$modified_time}";
		}
	}
	
	public function renderPartial($view, $params=array(), $return=false){
		if($view[0] != '/'){
			$view_folder_name = str_replace('Controller', '', get_class($this));
			$view_folder_name = strtolower($view_folder_name{0}) . substr($view_folder_name, 1);
			$view = '/' . $view_folder_name . '/' . $view;
		}
		$filepath = TCClick::app()->root_path . '/protected/views' . $view . '.php';
		return $this->renderFile($filepath, $params, $return);
	}
	
	public function renderCompatibleWithExternalSite($view, $params=array(), $return=false){
		$content = "";
		if($_GET['external_site_id']){
			$site = ExternalSite::findById($_GET['external_site_id']);
			if($site){
				$relative_uri = substr($_SERVER['REQUEST_URI'], strlen(TCClick::app()->root_url));
				$url = $site->url . $relative_uri;
				$url = preg_replace('/external_site_id=[^&]+&?/', '', $url);
				if($url[strlen($url)-1]=='?' || $url[strlen($url)-1]=='&'){
					$url = $url . "external_code=" . $site->code;
				}else{
					$url = $url . "&external_code=" . $site->code;
				}
				$content = HttpUtil::curl_get($url);
			}else{
				$content = "<h1 style='color:red'>external site not exist !</h1>";
			}
		}else if($_GET['external_code']){
			$user = User::current();
			if(!$user) $content = "<h1 style='color:red'>external site not exist !</h1>";
			else $content = $this->renderPartial($view, $params, true);
			echo $content;
			return;
		}else
			$content = $this->renderPartial($view, $params, true);
		$this->renderPartial('/layouts/' . $this->layout, array("content"=>$content), $return);
	}
	
	private function renderFile($filepath, $params, $return=false){
		extract($params);
		if($return){
			ob_start();
			include $filepath;
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}
		include $filepath;
	}
	
	public function render($view, $params=array(), $return=false){
		$content = $this->renderPartial($view, $params, true);
		$this->renderPartial('/layouts/' . $this->layout, array("content"=>$content), $return);
	}
	
	protected function isPost(){
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}
}

