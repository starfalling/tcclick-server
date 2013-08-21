<?php

/**
 * 外部站点管理
 * @author York.Gu <gyq5319920@gmail.com>
 */
class ExternalSitesController extends  Controller{
	public function filters(){
		return array(
				"LoginRequiredFilter",
		);
	}
	
	public function actionIndex(){
		$user = User::current();
		if($user->isAdmin()){
			$sites = ExternalSite::all();
		}else{
			$sites = ExternalSite::allForCurrentUser();
		}
		$this->render("index", array('sites'=>$sites));
	}
	
	public function actionDelete(){
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$site = ExternalSite::findById($_POST['id']);
			if($site && ($site->user_id==User::current()->id || User::current()->isAdmin())){ 
				// user can only delete his own external codes, or administrator can delete any code
				$site->delete();
			}
		}else header('Location: '.TCClick::app()->root_url.'externalSites/index');
		header('Location: '.$_SERVER['HTTP_REFERER']);
	}
	
	public function actionCreate(){
		$site = new ExternalSite();
		$error_message = null;
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$site->user_id = User::current()->id;
			$site->name = $_POST['name'];
			$site->url = $_POST['url'];
			$site->code = $_POST['code'];
			if($site->url[strlen($site->url)-1]!='/') $site->url .= '/';
			$info = $this->getInfoOfExternalCode($site->code, $site->url);
			if($info && $info->code==$site->code){
				$site->is_admin = $info->is_admin;
				$site->save();
				header('Location: '.TCClick::app()->root_url.'externalSites/index');
				return;
			}else $error_message = "无法获取到外站信息，请确认信息填写正确";
		}
		$this->render('create', array('site'=>$site, 'error_message'=>$error_message));
	}
	
	private function getInfoOfExternalCode($code, $root_url){
		$url = $root_url . 'externalCodes/ajaxInfo?code=' . $code;
		return json_decode(HttpUtil::curl_get($url));
	}
	
}

