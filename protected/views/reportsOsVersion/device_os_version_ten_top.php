<?php 
 $new_os_sql = "select version_id, sum(count) as sc from {counter_daily_new_os_version} GROUP BY version_id ORDER BY sc DESC LIMIT 10";
 $stmt = TCClick::app()->db->query($new_os_sql);
 $device_count = 0;
 foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
   $new_os_count[$row['version_id']] = $row['sc'];
   $device_count += $row['sc'];
 }
 
?>
<div class="block">
	<h3>操作系统分布明细</h3>
  <table>
    <thead>
      <th width="60%">操作系统版本</th>
      <th>比例</th>
    </thead>
    <?php foreach ($new_os_count as $key=>$count):?>
    <tr>
      <td><?php echo OsVersion::nameof($key)?></td>
      <td><?php echo round($count/$device_count*100,2)?>%</td>
    </tr>
    <?php endforeach;?>
  </table>
</div>