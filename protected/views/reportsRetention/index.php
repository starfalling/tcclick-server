<?php

/**
 * @var string $type
 */

$from = $_GET['from'] ? $_GET['from'] : date('Y-m-d', time() - 86400 * 30);
$to = $_GET['to'] ? $_GET['to'] : date('Y-m-d', time() - 86400);
$channel_id = intval($_GET['channel_id']);

$sql = null;
if($type == 'daily') {
  $sql = "select * from {retention_rate_daily} where `date`>='{$from}' and `date`<='{$to}'
          and channel_id={$channel_id} order by `date`";
} elseif($type == 'weekly') {
  $from = $_GET['from'] ? $_GET['from'] : date('Y-m-d', time() - 86400 * 90);
  $sql = "select * from {retention_rate_weekly} where `date`>='{$from}' and `date`<='{$to}'
          and channel_id={$channel_id} order by `date`";
} elseif($type == 'monthly') {
  $from = $_GET['from'] ? $_GET['from'] : date('Y-m-d', time() - 86400 * 365);
  $sql = "select * from {retention_rate_monthly} where `date`>='{$from}' and `date`<='{$to}'
          and channel_id={$channel_id} order by `date`";
}
$rows = array();
if(!empty($sql)) {
  $rows = TCClick::app()->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}
$type_names = array('daily' => '天', 'weekly' => '周', 'monthly' => '月');
$type_name = $type_names[$type];

?>
<h1>留存用户
  <?php
  $now = time();
  echo TCClickUtil::selector(array(
    array("label" => "最近一月", "from" => date("Y-m-d", $now - 86400 * 30), "to" => null),
    array("label" => "最近两月", "from" => date("Y-m-d", $now - 86400 * 60), "to" => null),
    array("label" => "最近三月", "from" => date("Y-m-d", $now - 86400 * 90), "to" => null),
    array("label" => "最近一年", "from" => date("Y-m-d", $now - 86400 * 365), "to" => null),
  ), array('from' => $from));


  $sql = "select * from {channels} order by id DESC";
  $stmt = TCClick::app()->db->query($sql);
  $channel_array = array();
  $channel_array[] = array("label" => "全部渠道", "channel_id" => "0");
  foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $channel_array[] = array("label" => $row['channel'], "channel_id" => $row['id']);
  }
  echo TCClickUtil::selector($channel_array);
  ?></h1>
<div class="block">
  <table class='retention'>
    <thead>
    <tr>
      <th style='width:100px;'>时间</th>
      <th style='width:100px;'>新增设备数</th>
      <th colspan="8">留存率(%):&nbsp;&nbsp;&nbsp;&nbsp;
        <select>
          <option id="day_retention" value='daily'>天</option>
          <option id="week_retention" value='weekly' <?php if($type == 'weekly') echo " selected='selected'" ?>>自然周
          </option>
          <option id="month_retention" value='monthly' <?php if($type == 'monthly') echo " selected='selected'" ?>>自然月
          </option>
        </select></th>
    </tr>
    </thead>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <?php for($i = 1; $i <= 8; $i++) { ?>
        <td style='text-align:center;padding-right:0;'><?php echo $i, $type_name ?>后</td>
      <?php } ?>
    </tr>
    <?php foreach($rows as $i => $row): ?>
      <tr>
      <td><?php
        if($type == 'monthly') {
          echo substr($row['date'], 0, 7), ' 月';
        } else
          echo $row['date'];
        ?></td>
      <td><?php echo $row['new_count'] ?></td>
      <td><?php if($row['retention1'] > 0) printf('%.02f', $row['retention1'] / 100) ?>&nbsp;</td>
      <td><?php if($row['retention2'] > 0) printf('%.02f', $row['retention2'] / 100) ?>&nbsp;</td>
      <td><?php if($row['retention3'] > 0) printf('%.02f', $row['retention3'] / 100) ?>&nbsp;</td>
      <td><?php if($row['retention4'] > 0) printf('%.02f', $row['retention4'] / 100) ?>&nbsp;</td>
      <td><?php if($row['retention5'] > 0) printf('%.02f', $row['retention5'] / 100) ?>&nbsp;</td>
      <td><?php if($row['retention6'] > 0) printf('%.02f', $row['retention6'] / 100) ?>&nbsp;</td>
      <td><?php if($row['retention7'] > 0) printf('%.02f', $row['retention7'] / 100) ?>&nbsp;</td>
      <td><?php if($row['retention8'] > 0) printf('%.02f', $row['retention8'] / 100) ?>&nbsp;</td>
      </tr><?php endforeach; ?>
  </table>
</div>


<script>$(function() {
    $("table.retention select").change(function() {
      var url = root_url + 'reportsRetention?type=' + this.options[this.selectedIndex].value;
      if(external_site_id) {
        url += '&external_site_id=' + external_site_id;
      }
      location.href = url;
    });
  });</script>