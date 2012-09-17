<?php

/**
 * @property DBConnection $db
 * @property CacheMemcache $cache
 * @property string $root_path
 * @author york
 */
class Application{
	public $db;
	public $root_path;
	public $root_url;
	private $cache;
	
	public function __construct(){
		include_once 'DBConnection.php';
		$this->db = new DBConnection();
		$this->root_path = dirname(dirname(dirname(__FILE__)));
		$this->root_url = dirname($_SERVER['SCRIPT_NAME']);
		if(DIRECTORY_SEPARATOR  == '\\'){ // windows
			$this->root_url = str_replace('\\', '/', $this->root_url);
		}
		if($this->root_url != "/") $this->root_url .= "/";
	}
	
	public function __get($name){
		if($name == "cache"){
			if(!$this->cache){
				include_once 'CacheMemcache.php';
				$this->cache = new CacheMemcache();
			}
			return $this->cache;
		}
	}
	
	private function route(){
		$uri = $_SERVER['REQUEST_URI'];
		$relative_path = substr($uri, strlen($this->root_url));
		if(!$relative_path) $relative_path = "/";
		$pos = strpos($relative_path, "?");
		if($pos) $relative_path = substr($relative_path, 0, $pos);
		global $router_config;
		foreach($router_config as $key=>$value){
			$key_reg = preg_replace('/<([\w]+):([^>]+)>/', '(?P<$1>$2)', $key);
			$key_reg = '/^' . str_replace('/', '\/', $key_reg) . '$/';
			if(preg_match($key_reg, $relative_path, $matches_key)){
				$route = $value;
				foreach($matches_key as $k=>$v){
					if(is_int($k)) continue;
					$route = str_replace("<{$k}>", $v, $route);
					$_GET[$k] = $v;
					$_REQUEST[$k] = $v;
				}
				$segments = explode("/", $route);
				if(count($segments) == 2){
					return $segments;
				}
			}
		}
	}
	
	public function run(){
		list($controller, $action) = $this->route();
		if($controller && $action){
			$class = ucfirst($controller) . 'Controller';
			$action = ucfirst($action);
			$method = 'action' . $action;
			$class_file_path = $this->root_path . '/protected/controllers/' . $class . '.php';
			if(file_exists($class_file_path)){
				include_once $class_file_path;
				$instance = new $class;
				if(method_exists($instance, $method)){
					if($this->preFilter($instance, $action)){
						$instance->$method();
						return;
					}else{
						header('HTTP/1.1 403 Not Found');
						echo "access denied";
						return;
					}
				}
			}
		}
		header('HTTP/1.1 404 Not Found');
		echo "controller or action not found";
	}
	
	private function preFilter($instance, $action){
		foreach($instance->filters() as $item){
			if(strpos($item, '-')){
				$segments = explode("-", $item);
				// 这些action不需要执行这个filter
				if(count($segments) == 2){
					foreach (explode(",", $segments[1]) as $excluded_action_name){
						$excluded_action_name = ucfirst(trim($excluded_action_name));
						if($excluded_action_name == $action) return true;
					}
				}
				$class = ucfirst(trim($segments[0]));
			}elseif(strpos($item, '+')){
				$segments = explode("+", $item);
				// 只有这些action需要执行这个filter
				if(count($segments) == 2){
					$need_execute = false;
					foreach (explode(",", $segments[1]) as $excluded_action_name){
						$excluded_action_name = ucfirst(trim($excluded_action_name));
						if($excluded_action_name == $action){
							$need_execute = true;
							break;
						}
					}
					if (!$need_execute) return true;
				}
				$class = ucfirst(trim($segments[0]));
			}else $class = ucfirst($item);
			if(!$class) continue;
			
			$class_file_path = $this->root_path . '/protected/filters/' . $class . '.php';
			if(file_exists($class_file_path)){
				include_once $class_file_path;
				$filter_instance = new $class;
				$filter_instance->controller_instance = $instance;
				$filter_instance->action_name = $action;
				if(!$filter_instance->preFilter()){
					return false;
				}
			}
		}
		return true;
	}
}

