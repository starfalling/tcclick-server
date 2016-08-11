<?php

class DeviceAndroidInfoName {
  private static $all_names = null;

  /**
   * get all the android info names from databases
   * @return self[]
   */
  public static function all() {
    if(self::$all_names === null) {
      self::reload();
    }

    return self::$all_names;
  }

  private static function reload($refreshCache = false) {
    if(!$refreshCache) {
      self::$all_names = TCClick::app()->cache->get('tcclick_all_device_android_info_names', false);
    } else {
      self::$all_names = false;
    }
    if(self::$all_names === false) {
      self::$all_names = array();
      $sql = "select * from {devices_android_info_names}";
      $stmt = TCClick::app()->db->query($sql);
      while(true) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$row) break;
        self::$all_names[$row['name']] = $row['id'];
      }
      TCClick::app()->cache->set('tcclick_all_device_android_info_names', self::$all_names);
    }

    return self::$all_names;
  }

  /**
   * add a android info name to database
   * @param string $name
   */
  public static function add($name) {
    $sql = "insert ignore into {devices_android_info_names} (name) values (:name)";
    TCClick::app()->db->execute($sql, array(":name" => $name));
    self::reload(true);
  }

  /**
   * query unique id of the android info name in database, create one if not exist
   * @param string $name
   * @return int
   */
  public static function idFor($name) {
    $all_names = self::all();
    if(!$all_names[$name]) {
      self::add($name);
      $all_names = self::all();
    }

    return $all_names[$name];
  }

  /**
   * query android info name by chanel id
   * @param integer $id
   * @return string
   */
  public static function nameOf($id) {
    foreach(self::all() as $name => $name_id) {
      if($name_id == $id) return $name;
    }
  }
}

