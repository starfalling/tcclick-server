<?php
class ReportsCarrierController extends Controller{
	public function filters(){
		return array("LoginRequiredFilter");
	}
	
	
  public function actionIndex(){
    $this->render('index');
  }
}