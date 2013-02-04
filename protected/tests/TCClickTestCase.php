<?php

class TCClickTestCase extends PHPUnit_Framework_TestCase{
	private function empty_all_tables(){
		$sql = "show tables";
		$result = TCClick::app()->db->query($sql);
		while(($table_name=$result->fetchColumn(0)) != NULL ){
			$sql = "truncate {$table_name}";
			TCClick::app()->db->execute($sql);
		}
	}
	
	
	protected function setUp(){
		parent::setUp();
		$this->empty_all_tables();
	}
	
	protected function importActivityData($data_file, $ip=null, $server_timestamp=null){
		$filepath = dirname(__FILE__) . '/data/' . $data_file;
		$this->assertTrue(file_exists($filepath), "file not exists: $filepath");
		$json_string = file_get_contents($filepath);
		$json = json_decode($json_string);
		$this->assertNotEmpty($json, "the content in file $filepath is not json");
		$gziped = gzcompress($json_string);
		
		if(!$ip) $ip = IpLocationSeekerTCClick::ip2int('222.66.37.26');
		if(!$server_timestamp) $server_timestamp = $json->timestamp;
		
		$params = array(':data_compressed'=>$gziped, ':ip'=>$ip, ':server_timestamp'=>$server_timestamp);
		$sql = "insert into {client_activities} (data_compressed, server_timestamp, ip) values
		(:data_compressed, :server_timestamp, :ip)";
		$this->assertEquals(1, TCClick::app()->db->execute($sql, $params));
	}
}

