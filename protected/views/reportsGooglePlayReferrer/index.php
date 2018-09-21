<?php

$yesterday = date('Y-m-d', time() - 86400);
$today = date('Y-m-d');
if($_GET['date']){
  $today = $_GET['date'];
  $yesterday = date('Y-m-d', strtotime($today) - 86400);
}


function loadSubchannelCounts($table_name, $field_name, $date) {
  $counts = array();
  $sql = "select * from {{$table_name}} where date='{$date}'";
  $stmt = TCClick::app()->db->query($sql);
  while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $channel_id = intval($row['channel_id']);
    $subchannel_id = intval($row[$field_name]);
    $count = intval($row['count']);
    if(!isset($counts[$channel_id])) {
      $counts[$channel_id] = array();
    }
    $counts[$channel_id][$subchannel_id] = $count;
  }

  return $counts;
}


$total_counts = Config::get(Config::KEY_DEVICE_COUNTS_WITH_ANDROID_INFO_SITE_ID);
$yesterday_new_counts = loadSubchannelCounts('counter_daily_new_with_android_info_site_id', 'site_id', $yesterday);
$yesterday_active_counts = loadSubchannelCounts('counter_daily_active_with_android_info_site_id', 'site_id', $yesterday);
$today_new_counts = loadSubchannelCounts('counter_daily_new_with_android_info_site_id', 'site_id', $today);
$today_active_counts = loadSubchannelCounts('counter_daily_active_with_android_info_site_id', 'site_id', $today);


?>
<h1>Google Play 子渠道分布 <input type="date " value="<?= $_GET['date']?$_GET['date']:$today; ?>"/></h1>
<div class="block">
  <h3>medium (Google) / site (AppsFlyer)</h3>
  <table>
    <thead>
    <tr>
      <th>渠道</th>
      <th style='width:80px'>子渠道</th>
      <th>总设备数</th>
      <th>昨日新增</th>
      <th>昨日活跃</th>
      <th>今日新增</th>
      <th>今日活跃</th>
      <th style='width:70px'>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $i = 0;
    foreach($total_counts as $channel_id => $subchannel_counts) {
      $channel_name_td_echoed = false;
      foreach($subchannel_counts as $site_id => $count) {
        $i++; ?>
        <tr>
          <?php if(!$channel_name_td_echoed): ?>
            <td rowspan="<?php echo count($subchannel_counts) ?>">
              <?php echo Channel::nameOf($channel_id) ?></td>
          <?php endif ?>
          <td style='word-break:break-all;'><?php echo DeviceAndroidInfoName::nameOf($site_id) ?></td>
          <td><?php echo $count ?></td>
          <td><?php echo $yesterday_new_counts[$channel_id][$site_id] ?></td>
          <td><?php echo $yesterday_active_counts[$channel_id][$site_id] ?></td>
          <td><?php echo $today_new_counts[$channel_id][$site_id] ?></td>
          <td><?php echo $today_active_counts[$channel_id][$site_id] ?></td>
          <?php if(!$channel_name_td_echoed): ?>
            <td rowspan="<?php echo count($subchannel_counts) ?>">
              <a href='<?php echo TCClick::app()->root_url,
              'reportsGooglePlayReferrer/view?channel_id=', $channel_id,
              '&field=site_id' ?>'>查看</a>
            </td>
          <?php endif ?>
        </tr>
        <?php
        $channel_name_td_echoed = true;
      }
    }
    ?>
    </tbody>
  </table>
</div>


<?php

$total_counts = Config::get(Config::KEY_DEVICE_COUNTS_WITH_ANDROID_INFO_CAMPAIGN_ID);
$yesterday_new_counts = loadSubchannelCounts('counter_daily_new_with_android_info_campaign_id', 'campaign_id', $yesterday);
$yesterday_active_counts = loadSubchannelCounts('counter_daily_active_with_android_info_campaign_id', 'campaign_id', $yesterday);
$today_new_counts = loadSubchannelCounts('counter_daily_new_with_android_info_campaign_id', 'campaign_id', $today);
$today_active_counts = loadSubchannelCounts('counter_daily_active_with_android_info_campaign_id', 'campaign_id', $today);

?>
<div class="block">
  <h3>campaign</h3>
  <table>
    <thead>
    <tr>
      <th>渠道</th>
      <th style='width:80px'>Campaign</th>
      <th>总设备数</th>
      <th>昨日新增</th>
      <th>昨日活跃</th>
      <th>今日新增</th>
      <th>今日活跃</th>
      <th style='width:70px'>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $i = 0;
    foreach($total_counts as $channel_id => $subchannel_counts) {
      $channel_name_td_echoed = false;
      foreach($subchannel_counts as $campaign_id => $count) {
        $i++; ?>
        <tr>
          <?php if(!$channel_name_td_echoed): ?>
            <td rowspan="<?php echo count($subchannel_counts) ?>">
              <?php echo Channel::nameOf($channel_id) ?></td>
          <?php endif ?>
          <td style='word-break:break-all;'><?php echo DeviceAndroidInfoName::nameOf($campaign_id) ?></td>
          <td><?php echo $count ?></td>
          <td><?php echo $yesterday_new_counts[$channel_id][$campaign_id] ?></td>
          <td><?php echo $yesterday_active_counts[$channel_id][$campaign_id] ?></td>
          <td><?php echo $today_new_counts[$channel_id][$campaign_id] ?></td>
          <td><?php echo $today_active_counts[$channel_id][$campaign_id] ?></td>
          <?php if(!$channel_name_td_echoed): ?>
            <td rowspan="<?php echo count($subchannel_counts) ?>">
              <a href='<?php echo TCClick::app()->root_url,
              'reportsGooglePlayReferrer/view?channel_id=', $channel_id,
              '&field=campaign_id' ?>'>查看</a>
            </td>
          <?php endif; ?>
        </tr>
        <?php
        $channel_name_td_echoed = true;
      }
    }
    ?>
    </tbody>
  </table>
</div>


<script>$(function() {
    if(external_site_id) {
      $(".block td a").each(function() {
        if(this.href.indexOf('?') != -1) this.href += "&external_site_id=" + external_site_id;
        else this.href += "?external_site_id=" + external_site_id;
      });
    }

    $("input[type=date]").change(function(){
      var date = $(this).val();
      var url = window.location.href;
      if(url.indexOf('?') != -1){
        window.location.href = url + "&date=" + date;
      }else{
        window.location.href = url + "?date=" + date;
      }
    });

  });</script>