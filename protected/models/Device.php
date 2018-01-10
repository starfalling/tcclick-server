<?php

class Device {
  public $id;
  public $udid;
  public $channel;
  public $channel_id;
  public $version;
  public $version_id;
  public $brand;
  public $model;
  public $model_id;
  public $os_version;
  public $os_version_id;
  public $resolution;
  public $resolution_id;
  public $carrier;
  public $carrier_id;
  public $network;
  public $network_id;
  public $referrer;

  public $is_new = false;
  public $is_update = false; // 是不是一个升级用户

  /**
   * save this model into database
   * @param string date and time when the device accessed
   */
  public function save($accessed_at = null) {
    if($this->id) return; // already saved into database

    $pid = $campaign = $af_siteid = '';
    if(!empty($this->referrer)) {
      $referrer_decoded = array();
      parse_str(urldecode($this->referrer), $referrer_decoded);
      if(!empty($referrer_decoded['pid'])) $pid = $referrer_decoded['pid'];
      if(!empty($referrer_decoded['c'])) $campaign = $referrer_decoded['c'];
      if(!empty($referrer_decoded['af_siteid'])) $af_siteid = $referrer_decoded['af_siteid'];
      if(!empty($referrer_decoded['utm_source'])) $pid = $referrer_decoded['utm_source'];
      if(!empty($referrer_decoded['utm_campaign'])) $campaign = $referrer_decoded['utm_campaign'];
      if(!empty($referrer_decoded['utm_medium'])) $af_siteid = $referrer_decoded['utm_medium'];
      if(!empty($referrer_decoded['campaigntype']) && $referrer_decoded['campaigntype']==='a') {
        // referrer 中包含 campaigntype=a 的为 adwords 广告的量
        $pid = 'adwords';
      }

      if(!empty($pid) && $pid != $this->channel) {
        $this->channel = $pid;
        $this->channel_id = Channel::idFor($pid);
      }
    }

    $sql = "select * from {devices} where udid=:udid";
    $stmt = TCClick::app()->db->query($sql, array(":udid" => $this->udid));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row) { // 这是一个老用户
      $this->id = $row['id'];
      if($this->version_id != $row['version_id']) { // 这是一个升级或者降级的用户
        $this->is_update = true;
      }
      if($this->is_update || $this->channel_id != $row['channel_id']) {
        $sql = "update {devices} 
                set version_id={$this->version_id},
                    channel_id={$this->channel_id}
                where id={$this->id}";
        TCClick::app()->db->execute($sql);
      }
    } else {
      $sql = "insert into {devices} (udid, channel_id, version_id, created_at) values
					(:udid, :channel_id, :version_id, :created_at)
					on duplicate key update channel_id=values(channel_id), id=last_insert_id(id)";
      $params = array(":udid" => $this->udid, ":channel_id" => $this->channel_id,
        ":created_at" => $accessed_at, ":version_id" => $this->version_id);
      $result = TCClick::app()->db->execute($sql, $params);
      $this->is_new = $result === 1; // 确定是插入到数据库里面了(并发环境下)
      $this->id = TCClick::app()->db->lastInsertId();
    }

    // track referrer data from google play install
    if($this->id && !empty($this->referrer)) {
      $sql = "select * from {devices_android_info} where id={$this->id}";
      $row = TCClick::app()->db->query($sql)->fetch(PDO::FETCH_ASSOC);
      if(empty($row)) {
        $sql = "insert ignore into {devices_android_info} (id, campaign_id, site_id, referrer)
                values (:id, :campaign_id, :site_id, :referrer)";
        TCClick::app()->db->execute($sql, array(
          ':id' => $this->id,
          ':campaign_id' => DeviceAndroidInfoName::idFor($campaign),
          ':site_id' => DeviceAndroidInfoName::idFor($af_siteid),
          ':referrer' => $this->referrer,
        ));
      }
    }
  }


  /**
   * 根据设备 ID 查询设备号
   * @param int[] $ids
   * @return string[]
   */
  public static function loadUdidsById($ids) {
    $udids = array();
    $sql = "select udid from {devices} where id in (" . join(",", $ids) . ")";
    $stmt = TCClick::app()->db->query($sql);
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $udids[] = $row['udid'];
    }

    return $udids;
  }
}

