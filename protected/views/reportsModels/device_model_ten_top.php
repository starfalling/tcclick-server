<?php 
/**
 *全部设备的信息
 */
$device_new_sql  = "select model_id, sum(count) as sc from {counter_daily_new_model} ";
$device_new_sql .= "group by model_id order by sc DESC LIMIT 10";
$stmt = TCClick::app()->db->query($device_new_sql);
$all_new_device = 0;
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
  $device_new_count[$row['model_id']] = $row['sc'];
  $all_new_device+=$row['sc'];
}
?>
<div class="block">
	<h3>设备型号分布明细</h3>
  <table>
    <thead>
      <th>设备型号</th>
      <th>设备比例</th>
    </thead>
    <?php foreach ($device_new_count as $key=> $count):?>
      <tr>
        <td><?php echo Model::nameof($key)?></td>
        <td><?php echo round($count/$all_new_device*100,2)?>%</td>
      </tr>
    <?php endforeach;?>
  </table>
</div>