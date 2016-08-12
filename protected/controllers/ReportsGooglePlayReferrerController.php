<?php

include_once "ReportsController.php";
class ReportsGooglePlayReferrerController extends Controller{
	public function filters(){
		return array(
				"LoginRequiredFilter",
				"ExternalAccessFilter - index",
		);
	}
	
	
	public function actionIndex(){
		$this->renderCompatibleWithExternalSite('index');
	}

}

