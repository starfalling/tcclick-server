<?php
class ReportsNetWorksController extends Controller{
	public function filters(){
		return array("AdminRequiredFilter");
	}
	
	
  public function actionIndex(){
    $this->render('index');
  }
}