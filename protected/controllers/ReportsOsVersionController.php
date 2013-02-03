<?php
class ReportsOsVersionController extends Controller{
	public function filters(){
		return array("AdminRequiredFilter");
	}
	
	
  public function actionIndex(){
    $this->render('index');
  }
  
}