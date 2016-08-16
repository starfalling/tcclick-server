<?php

class ReportsRetentionController extends Controller {
  public function filters() {
    return array(
      "AdminRequiredFilter",
      "ExternalAccessFilter - index",
    );
  }

  public function actionIndex() {
    $type = 'daily';
    if(in_array($_GET['type'], array('daily', 'weekly', 'monthly'))) {
      $type = $_GET['type'];
    }
    $this->renderCompatibleWithExternalSite("index", array('type' => $type));
  }

}