<?php 
$start_date = $_GET['from'] ? $_GET['from'] : date("Y-m-d", time()-86400*30);
$end_date = $_GET['to'] ? $_GET['to'] : date("Y-m-d", time());
$current_page = $_GET['page'] ? intval($_GET['page']) : 1;
$offset = ($current_page-1)*10;
$device_new_sql  = "select model_id, sum(count) as sc from {counter_daily_active_model}
where date>='$start_date' and date <='$end_date' 
group by model_id order by sc DESC LIMIT {$offset}, 10";
$stmt = TCClick::app()->db->query($device_new_sql);
$model_counts = array();
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
  $model_counts[$row['model_id']] = $row['sc'];
}

$sql = "select sum(count) from {counter_daily_active_model}
where date>='$start_date' and date <='$end_date'";
$all_devices_count = TCClick::app()->db->query($sql)->fetchColumn();
$sql = "select count(distinct model_id) from {counter_daily_active_model}
where date>='$start_date' and date <='$end_date'";
$items_count = TCClick::app()->db->query($sql)->fetchColumn();
$pages_count = $items_count%10==0 ? $items_count/10 : intval($items_count/10)+1;
?>
<div class="block">
	<h3>活跃设备型号分布明细</h3>
  <table>
    <thead>
      <th>设备型号</th>
      <th>设备比例</th>
    </thead>
    <?php foreach ($model_counts as $model_id => $count):?>
      <tr>
        <td><?php echo Model::nameof($model_id)?></td>
        <td><?php printf('%.02f', $count/$all_devices_count*100)?>%</td>
      </tr>
    <?php endforeach;?>
  </table>
  <?php TCClickUtil::pager($pages_count, $current_page)?>
</div>