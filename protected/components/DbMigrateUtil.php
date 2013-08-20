<?php

/**
 * This is a tool to migrate db to the latest version.
 * Different to other common migration tool, it has no downgrade methods.
 * @author York.Gu <gyq5319920@gmail.com>
 */
class DbMigrateUtil{
	const LATEST_DB_VERSION = 2;
	
	
	public function upgrade(){
		// check whether the database is inited or not
		$sql = "select * from {users} where id=1";
		$errorInfo = TCClick::app()->db->query($sql)->errorInfo();
		if($errorInfo && $errorInfo[0]=='42S02'){
			// table users not exists, the database is not inited
			$this->executeInitSql();
		}
		
		// find the current database version
		$current_db_version = Config::get(Config::KEY_CURRENT_DB_VERSION, -1);
		if($current_db_version == -1){
			$this->upgrade_1();
			Config::set(Config::KEY_CURRENT_DB_VERSION, 1);
			$current_db_version = 1;
		}
		
		// execute every database upgrade migrations
		for($version=$current_db_version+1; $version<=self::LATEST_DB_VERSION; $version++){
			$method_name = "upgrade_" . $version;
			$this->$method_name();
			Config::set(Config::KEY_CURRENT_DB_VERSION, $version);
		}
	}
	
	private function upgrade_1(){
		$sql = "create table {configs} (
				`id` integer primary key not null auto_increment,
				`key` varchar(255) not null default '',
				`value` mediumblob,
				unique key `key`(`key`)
		)engine myisam character set utf8";
		TCClick::app()->db->execute($sql);
	}
	
	private function upgrade_2(){
		$sql = "create table {external_codes} (
				`id` integer primary key not null auto_increment,
				`code` char(40) not null default '',
				`user_id` integer not null,
				`created_at` timestamp not null default current_timestamp,
				unique key `code`(`code`)
		)engine myisam character set utf8";
		TCClick::app()->db->execute($sql);
	}
	
	private function executeInitSql(){
		$content = file_get_contents(TCClick::app()->root_path . "/protected/data/init.sql");
		foreach(explode(";", $content) as $sql){
			$sql = trim($sql);
			if(empty($sql)) continue;
			TCClick::app()->db->execute($sql);
		}
		$this->importQqWry();
	}
	
	private function importQqWry(){
		$content = file_get_contents(TCClick::app()->root_path . "/protected/data/qqwry.sql");
		foreach(explode(";", $content) as $sql){
			$sql = trim($sql);
			if(empty($sql)) continue;
			TCClick::app()->db->execute($sql);
		}
	}
	
}

