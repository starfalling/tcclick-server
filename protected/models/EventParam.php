<?php

class EventParam{
	public $event_id;
	public $param_id;
	public $name_id;
	public $alias_id;
	
	public static function createIfNotExists($event_id, $name_id){
		$instance = self::loadByEventAndName($event_id, $name_id);
		if(!$instance){
			$sql = "select max(param_id) from {event_params} where event_id=:event_id";
			$max_param_id = TCClick::app()->db->query($sql, array(':event_id'=>$event_id))->fetchColumn(0);
			if(!$max_param_id) $max_param_id = 0;
			$param_id = $max_param_id + 1;
			
			$sql = "insert ignore into {event_params} (event_id, name_id, param_id) values
			(:event_id, :name_id, {$param_id})";
			$params = array(':event_id'=>$event_id, ':name_id'=>$name_id);
			if(TCClick::app()->db->execute($sql, $params) == 0){
				// 执行插入不成功，表明上面几行代码在执行的过程中，有其他进程插入了这条记录
				return self::createIfNotExists($event_id, $name_id);
			}else{
				$instance = new self;
				$instance->event_id = $event_id;
				$instance->name_id = $name_id;
				$instance->param_id = $param_id;
				$instance->alias_id = 0;
			}
		}
		return $instance;
	}

	public static function loadByEventAndName($event_id, $name_id){
		static $cached_event_params = null;
		if($cached_event_params === null){
			$cache_key = "tcclick_cached_event_params";
			$cached_event_params = TCClick::app()->cache->get($cache_key, array());
		}
		if(isset($cached_event_params[$event_id]) && isset($cached_event_params[$event_id][$name_id])){
			$row = $cached_event_params[$event_id][$name_id];
			$instance = new self;
			$instance->initWithDbRow($row);
			return $instance;
		}
		
		$sql = "select * from {event_params} where event_id=:event_id and name_id=:name_id";
		$params = array(':event_id'=>$event_id, ':name_id'=>$name_id);
		$row = TCClick::app()->db->query($sql, $params)->fetch(PDO::FETCH_ASSOC);
		if($row){
			$instance = new self;
			$instance->initWithDbRow($row);
			return $instance;
		}
	}
	
	private function initWithDbRow($row){
		$this->event_id = $row['event_id'];
		$this->param_id = $row['param_id'];
		$this->name_id = $row['name_id'];
		$this->alias_id = $row['alias_id'];
	}
}

