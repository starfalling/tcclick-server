<?php

class UsersController extends Controller{
	public function filters(){
		return array("LoginRequiredFilter");
		return array("AdminRequiredFilter - ChangePassword");
	}

	public function actionIndex(){
		$this->render("index", array('users'=>User::all()));
	}
	
	public function actionCreate(){
		if($this->isPost()){
			$this->saveUserFromPost(new User);
			return $this->redirect(TCClick::app()->root_url.'users');
		}
		$this->render("create", array('user'=>new User));
	}
	
	public function actionUpdate(){
		$user = User::findById($_GET['id']);
		if(!$user) return $this->redirect(TCClick::app()->root_url.'users');
		
		if($this->isPost()){
			$this->saveUserFromPost($user);
			return $this->redirect(TCClick::app()->root_url.'users');
		}
		$this->render("update", array('user'=>$user));
	}
	
	/**
	 * 禁用某一个账号
	 */
	public function actionBan(){
		$user_id = intval($_GET['id']);
		if($user_id){
			$sql = "update {users} set status=".User::STATUS_BANNED." where id={$user_id}";
			TCClick::app()->db->execute($sql);
			$sql = "delete from {access_tokens} where user_id={$user_id}";
			TCClick::app()->db->execute($sql);
		}
		$this->redirect($_SERVER['HTTP_REFERER']);
	}
	
	/**
	 * 恢复某一个账号
	 */
	public function actionRecover(){
		$user_id = intval($_GET['id']);
		if($user_id){
			$sql = "update {users} set status=".User::STATUS_NORMAL." where id={$user_id}";
			TCClick::app()->db->execute($sql);
		}
		$this->redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function actionChangePassword(){
		$user = User::current();
		if($_POST['old_password'] && $_POST['new_password']){
			// 验证用户输入的旧密码是否正确
			if(sha1($_POST['old_password'].$user->password_salt) == $user->password_sha1){
				$user->setPassword($_POST['new_password']);
				$user->save();
				$this->info = "密码修改成功";
			}else{
				$this->error = "原密码输入不正确";
			}
		}
		$this->render("change_password");
	}
	
	private function saveUserFromPost($user){
		$username = trim($_POST['username']);
		$password = trim($_POST['password']);
		
		if($username){
			$user->username = $username;
			if($password) $user->setPassword($password);
			$user->save();
			
			$sql = "delete from {user_channels} where user_id={$user->id}";
			TCClick::app()->db->execute($sql);
			
			foreach(explode(',', $_POST['channels']) as $channel_name){
				$channel_name = trim($channel_name);
				if(empty($channel_name)) continue;
				$channel_id = Channel::idFor($channel_name);
				if($channel_id){
					$sql = "insert ignore into {user_channels} (user_id, channel_id) values
					({$user->id}, $channel_id)";
					TCClick::app()->db->execute($sql);
				}
			}
		}
	}
}

