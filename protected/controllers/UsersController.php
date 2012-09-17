<?php

class UsersController extends Controller{
	public function filters(){
		return array("LoginRequiredFilter");
		return array("AdminRequiredFilter - index");
	}

	public function actionIndex(){
		
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
}

