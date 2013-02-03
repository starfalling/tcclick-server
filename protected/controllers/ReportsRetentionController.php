<?php
class ReportsRetentionController extends Controller{
	public function filters(){
		return array("AdminRequiredFilter");
	}
	
	public function actionIndex(){
		$this->render("index");
	}
	
}