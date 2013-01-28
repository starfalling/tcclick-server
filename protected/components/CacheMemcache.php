<?php

class CacheMemcache{
	private $mmc;
	private $is_memcached = true;
	
	public function __construct(){
		if(defined('SAE_TMP_PATH')){ // SAE
			$this->mmc = new Memcached();
			$this->is_memcached = true;
		}else{
			$this->is_memcached = false;
			$this->mmc = new Memcache();
			$this->mmc->connect(MEMCACHE_HOST, MEMCACHE_PORT);
			$this->mmc->setCompressThreshold(20000);
		}
		if (!$this->mmc){
			TCClick::error("memcache connect failed");
			die;
		}
	}
	
	
	public function get($key, $default=null){
		$value = $this->mmc->get($key);
		return $value===false ? $default : $value;
	}
	
	public function set($key, $value, $expire=0){
		if($this->is_memcached)
			return $this->mmc->set($key, $value, $expire);
		else 
			return $this->mmc->set($key, $value, 0, $expire);
	}
	
	public function delete($key){
		return $this->mmc->delete($key);
	}
	
	public function add($key, $value, $expire=0){
		if($this->is_memcached)
			return $this->mmc->add($key, $value, $expire);
		else
			return $this->mmc->add($key, $value, 0, $expire);
	}
	
	public function incr($key, $value=1, $create=false){
		$result = $this->mmc->increment($key, $value);
		if($result === false && $create){
			$this->add($key, 0);
			$result = $this->mmc->increment($key, $value);
		}
		return $result;
	}
}

