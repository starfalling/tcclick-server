<?php

class SiteController extends Controller{
	public function actionIndex(){
		if (User::current()){
			$this->redirect(TCClick::app()->root_url . 'reports');
		}else
			$this->render("index");
	}
	
	public function actionLogin(){
		if($_POST['username'] && $_POST['password']){
			$sql = "select * from {users} where username=:username and status=0";
			$row = TCClick::app()->db->query($sql, array(":username"=>$_POST['username']))->fetch(PDO::FETCH_ASSOC);
			if($row){ // 有这个用户
				if(sha1($_POST['password'] . $row['password_salt']) == $row['password_sha1']){
					// 密码正确，登录成功
					$access_token = $this->generateAccessToken($row['id']);
					$expire_at = date("Y-m-d H:i:s", time()+86400*7); // 默认一周的失效时间
					$sql = "insert into {access_tokens} (access_token, expire_at, user_id) values
					('{$access_token}', '{$expire_at}', {$row['id']})";
					TCClick::app()->db->execute($sql);
					setcookie("TCCLICK_ACCESS_TOKEN", $access_token, time()+86400*7, "/");
					header("Location: ".TCClick::app()->root_url."reports");
					exit;
				}
			}
		}
		$this->render("login");
	}
	
	public function actionLogout(){
		if($_COOKIE['TCCLICK_ACCESS_TOKEN']){
			$sql = "delete from {access_tokens} where access_token=:access_token";
			TCClick::app()->db->execute($sql, array(":access_token"=>$_COOKIE['TCCLICK_ACCESS_TOKEN']));
			setcookie("TCCLICK_ACCESS_TOKEN", "", strtotime("1999-01-01"));
		}
		header("Location: ".TCClick::app()->root_url);
	}
	
	private function generateAccessToken($user_id){
		$uniqid = uniqid("access_token-{$user_id}");
		return sha1($uniqid);
	}
}

