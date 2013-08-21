<?php
class ReportsOsVersionController extends Controller{
	public function filters(){
		return array(
				"AdminRequiredFilter",
				"ExternalAccessFilter - index",
		);
	}
	
	
  public function actionIndex(){
    $this->renderCompatibleWithExternalSite('index');
  }
  
}