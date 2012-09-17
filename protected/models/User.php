<?php

class User{
	public $username;
	public $id;
	public $password_salt;
	public $password_sha1;

	public static function current(){
		static $current_user = null;
		if(!$current_user && $_COOKIE['TCCLICK_ACCESS_TOKEN']){
			$sql = "select * from {access_tokens} where access_token=:access_token";
			$params = array(":access_token"=>$_COOKIE['TCCLICK_ACCESS_TOKEN']);
			$row = TCClick::app()->db->query($sql, $params)->fetch(PDO::FETCH_ASSOC);
			if($row){
				$user_id = $row['user_id'];
				$sql = "select * from {users} where id={$user_id}";
				$row = TCClick::app()->db->query($sql)->fetch(PDO::FETCH_ASSOC);
				$current_user = new self();
				$current_user->username = $row['username'];
				$current_user->password_salt = $row['password_salt'];
				$current_user->password_sha1 = $row['password_sha1'];
				$current_user->id = $row['id'];
			}
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
			(:username, :password_salt, :password_sha1)";
		}
		$params = array(
				":username" => $this->username,
				":password_salt" => $this->password_salt,
				":password_sha1" => $this->password_sha1,
		);
		TCClick::app()->db->execute($sql, $params);
	}


	private function randomPasswordSalt($length=4){
		static $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
		$salt = "";
		for($i=0; $i<$length; $i++){
			$salt .= $chars{rand(0, 63)};
		}
		return $salt;
	}
}

