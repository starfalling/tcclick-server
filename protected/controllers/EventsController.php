<?php

class EventsController extends  Controller{
	public function filters(){
		return array("AdminRequiredFilter");
	}

	public function actionIndex(){
		$this->render('index');
	}
	
	public function actionView(){
		$event = Event::loadById($_GET['id']);
		if($event){
			$this->render('view', array('event'=>$event));
		}else{
			$this->redirect(TCClick::app()->root_url . 'events');
		}
	}
	
}

