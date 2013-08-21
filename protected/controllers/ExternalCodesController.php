<?php

/**
 * 外部防伪码设定相关
 * @author York.Gu <gyq5319920@gmail.com>
 */
class ExternalCodesController extends  Controller{
	public function filters(){
		return array(
				"LoginRequiredFilter - ajaxInfo",
		);
	}
	
	/**
	 * get info of an external code
	 */
	public function actionAjaxInfo(){
		header("Content-type: application/json;charset=utf-8");
		$code = ExternalCode::findByCode($_GET['code']);
		$result = new stdClass();
		if($code){
			$result->code = $code->code;
			$result->is_admin = $code->getUser()->isAdmin();
		}
		echo json_encode($result);
	}
	
	public function actionIndex(){
		$user = User::current();
		if($user->isAdmin()){
			$codes = ExternalCode::all();
		}else{
			$codes = ExternalCode::allForCurrentUser();
		}
		$this->render("index", array('codes'=>$codes));
	}
	
	public function actionDelete(){
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$code = ExternalCode::findById($_POST['id']);
			if($code && ($code->user_id==User::current()->id || User::current()->isAdmin())){ 
				// user can only delete his own external codes, or administrator can delete any code
				ExternalCode::deleteById($_POST['id']);
			}
		}else header('Location: '.TCClick::app()->root_url.'externalCodes/index');
		header('Location: '.$_SERVER['HTTP_REFERER']);
	}
	
	public function actionCreate(){
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			ExternalCode::create();
		}else header('Location: '.TCClick::app()->root_url.'externalCodes/index');
		header('Location: '.$_SERVER['HTTP_REFERER']);
	}
	
}

