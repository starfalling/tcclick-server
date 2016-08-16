<?php

$yesterday = date('Y-m-d', time() - 86400);
$sql = "select * from {counter_daily_mutual_with_external_sites} where `date`='{$yesterday}'";
$yesterday_mutual_counts = array();
foreach(TCClick::app()->db->query($sql)->fetchAll() as $row) {
  $yesterday_mutual_counts[$row['external_site_id']] = array(
    'new' => $row['new_count'],
    'active' => $row['active_count'],
  );
}

$yesterday_counts = array();
$sql = "select * from {counter_daily_new} where `date`='{$yesterday}' and channel_id=" . Channel::CHANNEL_ID_ALL;
$row = TCClick::app()->db->query($sql)->fetch();
if(!empty($row)) {
  $yesterday_counts['new'] = $row['count'];
}
$sql = "select * from {counter_daily_active} where `date`='{$yesterday}' and channel_id=" . Channel::CHANNEL_ID_ALL;
$row = TCClick::app()->db->query($sql)->fetch();
if(!empty($row)) {
  $yesterday_counts['active'] = $row['count'];
}


?>

<h1>外部站共有设备数据</h1>
<div class="block">
  <table>
    <thead>
    <tr>
      <th>外部站</th>
      <th>昨日新增共有设备</th>
      <th>昨日活跃共有设备</th>
      <th style='width:70px'>操作</th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <td style="color:#4180B6;">本站</td>
      <td style="color:#4180B6;"><?php echo $yesterday_counts['new'] ?>(总新增)</td>
      <td style="color:#4180B6;"><?php echo $yesterday_counts['active'] ?>(总活跃)</td>
      <td>&nbsp;</td>
    </tr>
    <?php foreach(ExternalSite::all() as $site) {
      if($site->user_id != 1) continue;
      if(!$site->calculate_mutual_devices) continue; ?>
      <tr>
        <td><?php echo $site->name ?></td>
        <td><?php if($yesterday_mutual_counts[$site->id]) {
            echo $yesterday_mutual_counts[$site->id]['new'];
            if($yesterday_counts['new']) {
              $percentage = $yesterday_mutual_counts[$site->id]['new'] / $yesterday_counts['new'];
              printf(' (%.1f%%)', $percentage * 100);
            }
          } ?></td>
        <td><?php if($yesterday_mutual_counts[$site->id]) {
            echo $yesterday_mutual_counts[$site->id]['active'];
            if($yesterday_counts['active']) {
              $percentage = $yesterday_mutual_counts[$site->id]['active'] / $yesterday_counts['active'];
              printf(' (%.1f%%)', $percentage * 100);
            }
          } ?></td>
        <td>
          <?php
          $url = TCClick::app()->root_url . 'reportsExternalSiteMutualDevices/view?site_id=' . $site->id;
          ?>
          <a href='<?php echo $url ?>'>查看</a>
        </td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
  <p style="margin:5px 5px 5px 10px;">共有设备所指含义为设备上装有或者曾经装有过该APP</p>
</div>

<script>$(function() {
    if(external_site_id) {
      $(".block td a").each(function() {
        if(this.href.indexOf('?') != -1) this.href += "&external_site_id=" + external_site_id;
        else this.href += "?external_site_id=" + external_site_id;
      });
    }
  });</script>