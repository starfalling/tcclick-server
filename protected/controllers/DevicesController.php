<?php

class DevicesController extends Controller {
  public function filters() {
    return array(
      "LoginRequiredFilter",
      "AdminRequiredFilter",
    );
  }


  /**
   * 根据设备号查询有多少台设备是在系统当中的
   */
  public function actionQueryDevicesExistsCount() {
    header("Content-type: application/json;charset=utf-8");
    $count = 0;
    $input = file_get_contents('php://input');
    $udids = array();
    if(!empty($input)) {
      $udids = array();
      $length = strlen($input);
      for($i = 0; $i < $length; $i += 16) {
        $udid = bin2hex(substr($input, $i, 16));
        if(strlen($udid) != 32) continue;
        $udids[] = $udid;
      }
    }

    if(!empty($udids)) {
      $sql = "select count(*) as c from " . MYSQL_TABLE_PREFIX . "devices where udid in ("
        . join(',', array_fill(0, count($udids), '?'))
        . ")";
      $stmt = TCClick::app()->db->getDbSlave()->prepare($sql);
      foreach($udids as $i => $udid) {
        $stmt->bindValue($i + 1, $udid);
      }
      if($stmt->execute()) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = intval($row['c']);
      }
    }
    echo json_encode(array(
      'status' => 'success',
      'count' => $count,
    ));
  }
}

