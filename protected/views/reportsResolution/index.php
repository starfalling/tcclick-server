<?php 
$start_date = date('Y-m-d', time()-86400*30);
$end_date = date('Y-m-d');

// 所有分辨率的活跃设备总和
$sql = "select sum(count) as count from {counter_daily_active_resolution}
where date>='$start_date' and date<='$end_date'";
$all_active_count = TCClick::app()->db->query($sql)->fetchColumn();

// 按分辨率进行分组的前十
$sql = "select resolution_id, sum(count) as count from {counter_daily_active_resolution}
where date>='$start_date' and date<='$end_date' group by resolution_id
order by `count` desc limit 10";
$max_active_count = 0;
foreach (TCClick::app()->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row){
  $active_counts[$row['resolution_id']] = $row['count'];
  if($max_active_count < $row['count']) $max_active_count = $row['count'];
}


// 所有分辨率的新增设备总和
$sql = "select sum(count) as count from {counter_daily_new_resolution}
where date>='$start_date' and date<='$end_date'";
$all_new_count = TCClick::app()->db->query($sql)->fetchColumn();

// 按分辨率进行分组的前十
$sql = "select resolution_id, sum(count) as count from {counter_daily_new_resolution}
where date>='$start_date' and date<='$end_date' group by resolution_id
order by `count` desc limit 10";
$max_new_count = 0;
foreach (TCClick::app()->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row){
	$new_counts[$row['resolution_id']] = $row['count'];
	if($max_new_count < $row['count']) $max_new_count = $row['count'];
}



?>
<h1>分辨率</h1>
<div class="block">
  <h3>TOP 10 分辨率 <span class='right'><?php echo $start_date?> ~ <?php echo $end_date?> </span></h3>
  <ul class="tabs">
    <li id="tab_active_resolution" class="tab current">新增用户</li> 
    <li id="tab_new_resolution" class="tab">新增用户</li> 
  </ul>
  <div class="panels">
    <div id="panel_active_resolution" class="panel current">
      <table>
        <thead>
          <th width="190px">分辨率</th>
          <th>比例</th>
        </thead>
	       <?php if($active_counts): $i=0;foreach ($active_counts as $id=>$count):?><tr>
						<td><?php echo Resolution::nameof($id)?></td>
						<td class='percent'>
							<div class="label"><?php printf('%.02f', $count/$all_active_count*100)?>%</div>
							<div class="chart_area"><div style="width:<?php echo $count/$max_active_count*100?>%"></div></div>
						</td>
				</tr><?php $i++;if($i==10)break;endforeach;endif;?>
      </table>
    </div>
    <div id="panel_new_resolution" class="panel">
      <table>
        <thead>
          <th width="190px">分辨率</th>
          <th>比例</th>
        </thead>
	       <?php if($new_counts): $i=0;foreach ($new_counts as $id=>$count):?><tr>
						<td><?php echo Resolution::nameof($id)?></td>
						<td class='percent'>
							<div class="label"><?php printf('%.02f', $count/$all_new_count*100)?>%</div>
							<div class="chart_area"><div style="width:<?php echo $count/$max_new_count*100?>%"></div></div>
						</td>
				</tr><?php $i++;if($i==10)break;endforeach;endif;?>
      </table>
    </div>
  </div>
</div>