<?php

class User{
	public $username;
	public $id;
	public $password_salt;
	public $password_sha1;
	public $status;
	public $created_at;
	
	const STATUS_NORMAL = 0;
	const STATUS_BANNED = -1;
	
	public function isAdmin(){
		return $this->username == 'admin';
	}
	
	/**
	 * 获取数据库中所有的用户
	 */
	public static function all(){
		$users = array();
		$sql = "select * from {users}";
		$rows = TCClick::app()->db->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
		foreach($rows as $row){
			$user = new self();
			$user->initWithDbRow($row);
			$users[] = $user;
		}
		return $users;
	}
	
	/**
	 * 根据用户ID加载用户的信息
	 * @param integer $id
	 * @return User
	 */
	public static function findById($id){
		$sql = "select * from {users} where id=".intval($id);
		$row = TCClick::app()->db->query($sql)->fetch(PDO::FETCH_ASSOC);
		if($row){
			$user = new self();
			$user->initWithDbRow($row);
			return $user;
		}
		return null;
	}
	
	/**
	 * 获取该用户有权限进行查看的渠道号
	 */
	public function getChannelIds(){
		$sql = "select * from {user_channels} uc 
		where uc.user_id=".intval($this->id);
		$channel_ids = array();
		$rows = TCClick::app()->db->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
		foreach($rows as $row){
			$channel_ids[] = $row['channel_id'];
		}
		return $channel_ids;
	}

	/**
	 * 获取当前登录用户
	 */
	public static function current(){
		static $current_user = null;
		if(!$current_user && $_COOKIE['TCCLICK_ACCESS_TOKEN']){
			$sql = "select * from {access_tokens} where access_token=:access_token";
			$params = array(":access_token"=>$_COOKIE['TCCLICK_ACCESS_TOKEN']);
			$row = TCClick::app()->db->query($sql, $params)->fetch(PDO::FETCH_ASSOC);
			if($row) $current_user = self::findById($row['user_id']);
		}
		if($current_user && !$current_user->isAdmin() && $current_user->status==self::STATUS_BANNED){
			// 已被禁用的普通账号
			return null;
		}
		return $current_user;
	}
	
	public function setPassword($password){
		$this->password_salt = $this->randomPasswordSalt();
		$this->password_sha1 = sha1($password . $this->password_salt);
	}
	
	public function save(){
		if($this->id){
			$sql = "update {users} set username=:username, password_salt=:password_salt,
			password_sha1=:password_sha1 where id={$this->id}";
		}else{
			$sql = "insert into {users} (username, password_salt, password_sha1) values
			(:username, :password_salt, :password_sha1) on duplicate key update 
			id=last_insert_id(id), password_salt=values(password_salt),
			password_sha1=values(password_sha1)";
		}
		$params = array(
				":username" => $this->username,
				":password_salt" => $this->password_salt,
				":password_sha1" => $this->password_sha1,
		);
		TCClick::app()->db->execute($sql, $params);
		if(!$this->id){
			$this->id = TCClick::app()->db->lastInsertId();
		}
	}


	private function randomPasswordSalt($length=4){
		static $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
		$salt = "";
		for($i=0; $i<$length; $i++){
			$salt .= $chars{rand(0, 63)};
		}
		return $salt;
	}
	
	private function initWithDbRow($row){
		$this->username = $row['username'];
		$this->id = $row['id'];
		$this->status = $row['status'];
		$this->password_salt = $row['password_salt'];
		$this->password_sha1 = $row['password_sha1'];
		$this->created_at = $row['created_at'];
	}
}

