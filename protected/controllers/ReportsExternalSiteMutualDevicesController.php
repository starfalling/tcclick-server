<?php

include_once "ReportsController.php";

class ReportsExternalSiteMutualDevicesController extends Controller {
  public function filters() {
    return array(
      "LoginRequiredFilter",
      "AdminRequiredFilter",
      "ExternalAccessFilter - index, view",
    );
  }


  public function actionIndex() {
    $this->renderCompatibleWithExternalSite('index');
  }


  public function actionView() {
    $external_site = ExternalSite::findById($_GET['site_id']);
    if($external_site || isset($_GET['external_site_id'])) {
      $this->renderCompatibleWithExternalSite('view', array(
        'external_site' => $external_site,
      ));
    } else {
      $this->redirect(TCClick::app()->root_url . 'reportsExternalSiteMutualDevices');
    }
  }


  public function actionAjaxDailyCountsSpline() {
    header("Content-type: application/json;charset=utf-8");
    $today = date("Y-m-d");
    if(!empty($_GET['from'])) {
      $start_date = date('Y-m-d', strtotime($_GET['from']));
    } else
      $start_date = date("Y-m-d", time() - 86400 * 30);
    $end_date = $today;
    $json = array("stats" => array(), "result" => "success");
    $external_site_id = intval($_GET['site_id']);
    if(!in_array($_GET['type'], array('new', 'active'))) {
      // 传入参数不正确
      echo json_encode($json);

      return;
    }

    // 外部站
    $daily_mutual_counts = ReportsController::generateZeroDailyCount($start_date, $end_date);
    $sql = "select * from {counter_daily_mutual_with_external_sites}
            where `date`>=:start and `date`<=:end and external_site_id={$external_site_id}";
    $stmt = TCClick::app()->db->query($sql, array(":start" => $start_date, ":end" => $end_date));
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
      $daily_mutual_counts[$row['date']] = intval($row[$_GET['type'] . '_count']);
    }
    $json['stats'][] = array(
      "data" => array_values($daily_mutual_counts),
      "name" => '共有设备数',
    );

    // 本站
    $daily_self_counts = ReportsController::generateZeroDailyCount($start_date, $end_date);
    $sql = "select * from {counter_daily_{$_GET['type']}} where `date`>=:start and `date`<=:end
            and channel_id=" . Channel::CHANNEL_ID_ALL;
    $stmt = TCClick::app()->db->query($sql, array(":start" => $start_date, ":end" => $end_date));
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
      $daily_self_counts[$row['date']] = intval($row['count']);
    }
    $json['stats'][] = array(
      "data" => array_values($daily_self_counts),
      "name" => '本站' . ($_GET['type'] == 'new' ? '新增' : '活跃'),
    );

    $json['dates'] = ReportsController::datesArrayForJsonOutput($start_date, $end_date);
    echo json_encode($json);
  }

}

