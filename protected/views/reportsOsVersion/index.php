<?php
$time = time();
$start_date = date('Y-m-d', $time-86400*30);
$end_date = date('Y-m-d',$time);
$active_os_sql  = "select version_id , sum(count) as sc from {counter_daily_active_os_version} 
	where date>='$start_date' and date<='$end_date'
	group by version_id ORDER BY sc DESC LIMIT 10";
$stmt = TCClick::app()->db->query($active_os_sql);
$max_active_count = 0;
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
  $active_os_counts[$row['version_id']] = $row['sc'];
  if ($max_active_count<$row['sc']) $max_active_count = $row['sc'];
}
$sql = "select sum(count) from {counter_daily_active_os_version}
where date>='$start_date' and date <='$end_date'";
$all_active_count = TCClick::app()->db->query($sql)->fetchColumn(0);

$new_sql  = "select version_id, sum(count) as sc from {counter_daily_new_os_version} 
	where date>='$start_date' and date<='$end_date'
	group by version_id ORDER BY sc DESC LIMIT 10";
$stmt = TCClick::app()->db->query($new_sql);
$max_new_count = 0;
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
  $new_os_counts[$row['version_id']] = $row['sc'];
  if ($max_new_count<$row['sc']) $max_new_count = $row['sc'];
}
$sql = "select sum(count) from {counter_daily_new_os_version}
where date>='$start_date' and date <='$end_date'";
$all_new_count = TCClick::app()->db->query($sql)->fetchColumn(0);
?>
<h1>操作系统 </h1>
<div class="block">
  <h3>TOP 10 操作系统   <span style="float: right;"><?php echo $start_date?> ~ <?php echo $end_date?> </span></h3>
  <ul class="tabs">
    <li id="$tab_active_os" class="tab current">活跃用户</li>
    <li id="$tab_new_os" class="tab">新增用户</li>
  </ul>
  <div class="panels">
    <div id="panel_active_os" class="panel current">
      <table>
       	<thead>
          <th width="190px">版本</th>
          <th>比例</th>
        </thead>
       <?php if($active_os_counts): $i=0;foreach ($active_os_counts as $version_id=>$count):?><tr>
					<td><?php echo OsVersion::nameof($version_id)?></td>
					<td class='percent'>
						<div class="label"><?php printf('%.02f', $count/$all_active_count*100)?>%</div>
						<div class="chart_area"><div style="width:<?php echo $count/$max_active_count*100?>%"></div></div>
					</td>
			</tr><?php $i++;if($i==10)break;endforeach;endif;?>
      </table>
    </div>
    <div id="panel_new_os" class="panel">
      <table>
        <thead>
          <th width="190px">版本</th>
          <th>比例</th>
        </thead>
       <?php $i=0;foreach ($new_os_counts as $version_id=>$count):?><tr>
					<td><?php echo OsVersion::nameof($version_id)?></td>
					<td class='percent'>
						<div class="label"><?php printf('%.02f', $count/$all_new_count*100)?>%</div>
						<div class="chart_area"><div style="width:<?php echo $count/$max_new_count*100?>%"></div></div>
					</td>
			</tr><?php $i++;if($i==10)break;endforeach;?>
      </table>
    </div>
  </div>
</div>


<?php include_once 'device_os_version_ten_top.php';?>
