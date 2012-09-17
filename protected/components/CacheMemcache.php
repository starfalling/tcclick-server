<?php

class CacheMemcache{
	private $mmc;
	
	public function __construct(){
		if(defined('SAE_TMP_PATH')){ // SAE
			$this->mmc = memcache_init();
		}else{
			$this->mmc = memcache_connect(MEMCACHE_HOST, MEMCACHE_PORT);
		}
		if (!$this->mmc){
			TCClick::error("memcache connect failed");
			die;
		}
		memcache_set_compress_threshold($this->mmc, 20000);
	}
	
	
	public function get($key, $default=null){
		$value = memcache_get($this->mmc, $key);
		return $value===false ? $default : $value;
	}
	
	public function set($key, $value, $expire=0){
		return memcache_set($this->mmc, $key, $value, false, $expire);
	}
	
	public function delete($key){
		return memcache_delete($this->mmc, $key);
	}
	
	public function add($key, $value, $expire=0){
		return memcache_add($this->mmc, $key, $value, false, $expire);
	}
}

