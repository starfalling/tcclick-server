<?php

include_once "ReportsController.php";

class ReportsGooglePlayReferrerController extends Controller {
  public function filters() {
    return array(
      "LoginRequiredFilter",
      "ExternalAccessFilter - index, view",
    );
  }


  public function actionIndex() {
    $this->renderCompatibleWithExternalSite('index');
  }


  public function actionView() {
    $channel_name = Channel::nameOf($_GET['channel_id']);
    if(($channel_name || isset($_GET['external_site_id'])) && !empty($_GET['field'])) {
      $this->renderCompatibleWithExternalSite('view', array(
        'channel_name' => $channel_name,
        'channel_id' => intval($_GET['channel_id']),
      ));
    } else {
      $this->redirect(TCClick::app()->root_url . 'reportsGooglePlayReferrer');
    }
  }


  public function actionAjaxDailyCountsSpline() {
    header("Content-type: application/json;charset=utf-8");
    $start_date = $_GET['start_date'] ? $_GET['start_date'] : date("Y-m-d", time() - 86400 * 30);
    $end_date = $_GET['end_date'] ? $_GET['end_date'] : date("Y-m-d", time());
    $json = array("stats" => array(), "result" => "success");
    $daily_counts = array();
    $channel_id = intval($_GET['channel_id']);
    if(!in_array($_GET['type'], array('new', 'active'))) {
      // 传入参数不正确
      echo json_encode($json);

      return;
    }
    if(!in_array($_GET['field'], array('site_id', 'campaign_id'))) {
      // 传入参数不正确
      echo json_encode($json);

      return;
    }

    $user = User::current();
    if(!$user->isAdmin()) {
      $channel_ids = $user->getChannelIds();
      if(!in_array($channel_id, $channel_ids)) {
        // 没有权限访问
        echo json_encode($json);

        return;
      }
    }

    $field_name = $_GET['field'];
    $sql = "select * from {counter_daily_{$_GET['type']}_with_android_info_{$field_name}} 
            where `date`>=:start and `date`<=:end and channel_id={$channel_id}";
    $stmt = TCClick::app()->db->query($sql, array(":start" => $start_date, ":end" => $end_date));
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
      if(!isset($daily_counts[$row[$field_name]])) {
        $daily_counts[$row[$field_name]] = ReportsController::generateZeroDailyCount($start_date, $end_date);
      }

      $daily_counts[$row[$field_name]][$row['date']] += intval($row['count']);
    }
    foreach($daily_counts as $subchannel_id => $counts) {
      $json['stats'][] = array(
        "data" => array_values($counts),
        "name" => DeviceAndroidInfoName::nameOf($subchannel_id),
      );
    }
    $json['dates'] = ReportsController::datesArrayForJsonOutput($start_date, $end_date);
    echo json_encode($json);
  }

}

