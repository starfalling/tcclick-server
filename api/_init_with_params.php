<?php

$date = null;
if(!empty($_GET['date'])) $date = $_GET['date'];
if(!empty($argv[1])) $date = $argv[1];
if($date == 'yesterday') {
  $date = date('Y-m-d', time() - 86400);
} elseif(empty($date) || !preg_match(RegPattern::DATE, $date)) {
  $date = date('Y-m-d');
}

