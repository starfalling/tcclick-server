<?php

include "AnalyzeListenerInitDevice.php";
include "AnalyzeListenerNewDevice.php";
include "AnalyzeListenerVersionUpdateDevice.php";
include "AnalyzeListenerActiveDevice.php";
include "AnalyzeListenerUsage.php";
include "AnalyzeListenerException.php";

/**
 * @author york
 */
class Analyzer{
	/** 
	 * 服务器收到的客户端收集的json数据
	 * @var stdClass
	 */
	public $json;
	/** 
	 * 服务器接收到客户端发来的数据时，服务器端的时间戳
	 * @var int 
	 */
	public $server_timestamp;
	/** 
	 * 客户端跟服务器端之间的时间偏移差值
	 * @var int
	 */
	public $client_timeoffset;
	/** 
	 * 发来数据包的客户端在数据库当中的相关信息
	 * @var Device
	 */
	public $device;
	/**
	 * 发来数据包的客户端的IP地址
	 * @var int
	 */
	public $ip_int;
	/**
	 * 发来数据包的客户端的IP地址所对应的区域ID
	 * @var int
	 */
	public $area_id;
	
	private $listeners;
	
	public function __construct($server_timestamp, $ip_int, $data_uncompressed){
		$this->server_timestamp = $server_timestamp;
		$this->ip_int = $ip_int;
		$this->area_id = Area::idFor(IpLocationSeekerTCClick::seek($this->ip_int));
		$this->json = json_decode($data_uncompressed);
		$this->listeners = array();
		$this->listeners[] = new AnalyzeListenerInitDevice();
		$this->listeners[] = new AnalyzeListenerNewDevice();
		$this->listeners[] = new AnalyzeListenerVersionUpdateDevice();
		$this->listeners[] = new AnalyzeListenerActiveDevice();
		$this->listeners[] = new AnalyzeListenerUsage();
		$this->listeners[] = new AnalyzeListenerException();
	}

	public function analyze(){
		if(!$this->json) return;
		$this->client_timeoffset = $this->server_timestamp - $this->json->timestamp;
		$activities = $this->json->data->activities; // 修正时间偏移
		if($activities){
			foreach($activities as $activity){
				$activity->start_at += $analyze->client_timeoffset;
				if($activity->start_at > time()+86400) return; // 收集上来的客户端时间太过超前了，丢弃这样的数据
				$activity->end_at += $analyze->client_timeoffset;
				$activity->seconds_spent = $activity->end_at - $activity->start_at;
			}
		}
		
		
		foreach($this->listeners as $listener){
			$listener->execute($this);
		}
	}
}



interface IAnalyzeListener{
	/**
	 * @param Analyzer $analyze
	 */
	public function execute($analyze);
}