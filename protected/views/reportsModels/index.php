<?php 


$start_date = $_GET['from'] ? $_GET['from'] : date("Y-m-d", time()-86400*30);
$end_date = $_GET['to'] ? $_GET['to'] : date("Y-m-d", time());
$active_sql = "select model_id, sum(count) as sc from {counter_daily_active_model} 
	where date>='$start_date' and date <='$end_date'
	group by model_id order by sc DESC LIMIT 50";
$stmt = TCClick::app()->db->query($active_sql);
$max_active_device_count = 0;
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
	$model_id = $row['model_id'];
	$name = Model::readableNameof($model_id);
  $active_device_counts[$name] += $row['sc'];
  if ($max_active_device_count < $active_device_counts[$name]){
  	$max_active_device_count = $active_device_counts[$name];
  }
}
if($active_device_counts) arsort($active_device_counts);
$sql = "select sum(count) from {counter_daily_active_model} where date>='$start_date' and date <='$end_date'";
$all_active_device_count = TCClick::app()->db->query($sql)->fetchColumn(0);

$new_sql = "select model_id, sum(count) as sc from {counter_daily_new_model} 
	where date>='$start_date' and date <='$end_date'
	group by model_id order by sc DESC LIMIT 50";
$stmt = TCClick::app()->db->query($new_sql);
$max_new_device = 0;
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
	$model_id = $row['model_id'];
	$name = Model::readableNameof($model_id);
	$new_device_counts[$name] += $row['sc'];
	if ($max_new_device < $new_device_counts[$name]){
		$max_new_device = $new_device_counts[$name];
	}
}
arsort($new_device_counts);
$sql = "select sum(count) from {counter_daily_new_model} where date>='$start_date' and date <='$end_date'";
$all_new_device = TCClick::app()->db->query($sql)->fetchColumn(0);


// iOS 越狱图表
$show_jailbroken = false;
$jailbroken_percent = 0;
TCClick::app()->db->query("select * from {counter_daily_active_jailbroken} limit 1", null, $errorInfo);
if(!$errorInfo){
	$show_jailbroken = true;
	
	$sql = "select sum(count) from {counter_daily_active_jailbroken} 
	where date>='$start_date' and date <='$end_date'
	and jailbroken=1";
	$jailbroken_count = TCClick::app()->db->query($sql)->fetchColumn(0);

	$sql = "select sum(count) from {counter_daily_active_jailbroken} 
	where date>='$start_date' and date <='$end_date'";
	$all_count = TCClick::app()->db->query($sql)->fetchColumn(0);
	$jailbroken_percent = $jailbroken_count / $all_count;
}

?>
<h1>设备</h1>
<div class="block">
  <h3>TOP 20设备型号 <span class='right'><?php echo $start_date?> ~ <?php echo $end_date?></span></h3>
  <ul class="tabs">
    <li id="tab_active_device" class="tab current">活跃设备</li>
    <li id="tab_new_device" class="tab">新增设备</li>
    <?php if($show_jailbroken):?>
    <li id="tab_jailbroken" class="tab">越狱比例</li>
    <?php endif;?>
  </ul>
  <div class="panels">
    <div id="pan_active_device" style='height:auto' class="panel current">
      <table>
				<thead>
					<th width="190">型号</th>
					<th>比例</th>
				</thead>
       <?php if($active_device_counts): $i=0;foreach ($active_device_counts as $name=>$count):?><tr>
					<td><?php echo $name?></td>
					<td class='percent'>
						<div class="label"><?php printf('%.02f', $count/$all_active_device_count*100)?>%</div>
						<div class="chart_area"><div style="width:<?php echo $count/$max_active_device_count*100?>%"></div></div>
					</td>
			</tr><?php $i++;if($i==20)break;endforeach;endif;?>
    </table>
    </div>
    <div id="pan_new_device" style='height:auto' class="panel">
      <table>
				<thead>
					<th width="190">型号</th>
					<th>比例</th>
				</thead>
        <?php if($new_device_counts): $i=0;foreach ($new_device_counts as $name=>$count):?><tr>
					<td><?php echo $name?></td>
					<td class='percent'>
						<div class="label"><?php printf('%.02f', $count/$all_new_device*100)?>%</div>
						<div class="chart_area"><div style="width:<?php echo $count/$max_new_device*100?>%"></div></div>
					</td>
				</tr><?php $i++;if($i==20)break;endforeach;endif;?>
    </table>
    </div>
    <?php if($show_jailbroken):?>
    <div id="pan_jailbroken" style='height:auto' class="panel">
      <table>
				<thead>
					<th width="190">是否越狱</th>
					<th>比例</th>
				</thead>
				<tr>
					<td>已越狱</td>
					<td class='percent'>
						<div class="label"><?php printf('%.02f', $jailbroken_percent*100)?>%</div>
						<div class="chart_area"><div style="width:<?php echo $jailbroken_percent*100?>%"></div></div>
					</td>
				</tr>
				<tr>
					<td>未越狱</td>
					<td class='percent'>
						<div class="label"><?php printf('%.02f', 100-$jailbroken_percent*100)?>%</div>
						<div class="chart_area"><div style="width:<?php echo 100-$jailbroken_percent*100?>%"></div></div>
					</td>
				</tr>
    </table>
    </div>
    <?php endif;?>
  </div> 
</div>

<div id="model_list_block" class="ajax_pager_container">
</div>
<script>$(function(){
	<?php $url = TCClick::app()->root_url . 'reportsModels/AjaxListBlock?from='.$start_date.'&to='.$end_date?>
	$("#model_list_block").load('<?php echo $url?>');
})</script>
