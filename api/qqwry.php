<?php

include_once dirname(dirname(__FILE__)) . '/protected/init.php';

$qqwry_filepath = dirname(__FILE__) . "/protected/data/QQWry.Dat";
$seeker = new IpLocationSeekerBinary($qqwry_filepath);
$seeker->saveToTCClickMysql();
