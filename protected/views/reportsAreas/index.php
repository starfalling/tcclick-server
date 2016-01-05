<?php
$from = $_GET['from'] ? $_GET['from'] : date('Y-m-d', time() - 30*86400);
$to = $_GET['to'] ? $_GET['to'] : date('Y-m-d');

// 所有活跃设备总和
$sql = "select sum(count) as count from {counter_daily_active_area}
where date>='{$from}' and date<='{$to}'";
$all_active_count = TCClick::app()->db->query($sql)->fetchColumn();

// 中国的所有活跃设备总和
$sql = "select sum(count) as count from {counter_daily_active_area}
where date>='{$from}' and date<='{$to}' and area_id<=35";
$china_all_active_count = TCClick::app()->db->query($sql)->fetchColumn();
$world_active_counts[1] = $china_all_active_count;
$max_world_active_count = $china_all_active_count;

// 其他国家的活跃设备数目
$sql = "select sum(count) as count, area_id from {counter_daily_active_area}
where date>='{$from}' and date<='{$to}' and area_id>35 group by area_id 
order by `count` desc limit 10";
foreach(TCClick::app()->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row){
	$world_active_counts[$row['area_id']] = $row['count'];
	if($max_world_active_count < $row['count']) $max_world_active_count = $row['count'];
}
arsort($world_active_counts);


// 所有新增设备总和
$sql = "select sum(count) as count from {counter_daily_new_area}
where date>='{$from}' and date<='{$to}'";
$all_new_count = TCClick::app()->db->query($sql)->fetchColumn();

// 中国的所有新增设备总和
$sql = "select sum(count) as count from {counter_daily_new_area}
where date>='{$from}' and date<='{$to}' and area_id<=35";
$china_all_new_count = TCClick::app()->db->query($sql)->fetchColumn();
$world_new_counts[1] = $china_all_new_count;
$max_world_new_count = $china_all_new_count;

// 其他国家的新增设备数目
$sql = "select sum(count) as count, area_id from {counter_daily_new_area}
where date>='{$from}' and date<='{$to}' and area_id>35 group by area_id 
order by `count` desc limit 10";
foreach(TCClick::app()->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row){
	$world_new_counts[$row['area_id']] = $row['count'];
	if($max_world_new_count < $row['count']) $max_world_new_count = $row['count'];
}
arsort($world_new_counts);




// 省份活跃分布
$sql = "select sum(count) as count, area_id from {counter_daily_active_area}
where date>='{$from}' and date<='{$to}' and area_id>1 and area_id<=35 group by area_id 
order by `count` desc limit 20";
foreach(TCClick::app()->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row){
	$province_active_counts[$row['area_id']] = $row['count'];
	if($max_province_active_count < $row['count']) $max_province_active_count = $row['count'];
}
// 省份新增分布
$sql = "select sum(count) as count, area_id from {counter_daily_new_area}
where date>='{$from}' and date<='{$to}' and area_id>1 and area_id<=35 group by area_id 
order by `count` desc limit 20";
foreach(TCClick::app()->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row){
	$province_new_counts[$row['area_id']] = $row['count'];
	if($max_province_new_count < $row['count']) $max_province_new_count = $row['count'];
}



?>
<h1>地域
<?php
$now = time();
echo TCClickUtil::selector(array(
		array("label"=>"昨天", "from"=>date("Y-m-d", $now-86400), "to"=>date("Y-m-d", $now-86400)),
		array("label"=>"前天", "from"=>date("Y-m-d", $now-86400*2), "to"=>date("Y-m-d", $now-86400*2)),
		array("label"=>"最近一月", "from"=>date("Y-m-d", $now-86400*30), "to"=>null),
		array("label"=>"最近两月", "from"=>date("Y-m-d", $now-86400*60), "to"=>null),
		array("label"=>"最近三月", "from"=>date("Y-m-d", $now-86400*90), "to"=>null),
		array("label"=>"最近一年", "from"=>date("Y-m-d", $now-86400*365), "to"=>null),
), array('from'=>date("Y-m-d", $now-86400*30)))?></h1>

<div class="block">
	<h3>TOP 10 国家分布<span class='right'><?php echo $from?> ~ <?php echo $to?></span></h3>
	<ul class="tabs">
		<li id="tab_active_devices" class="tab current">活跃设备</li>
		<li id="tab_new_devices" class="tab">新增设备</li>
	</ul>
	<div class="panels">
		<div id="panel_active_devices" class="panel current">
			<table>
				<thead>
					<th width="150">国家</th>
					<th width="150">数量</th>
					<th>比例</th>
				</thead>
       <?php if($all_active_count): $i=0;foreach ($world_active_counts as $area_id=>$count):?><tr>
					<td><?php echo Area::nameof($area_id)?></td>
				  <td><?php echo $count?></td>
					<td class='percent'>
						<div class="label"><?php printf('%.02f', $count/$all_active_count*100)?>%</div>
						<div class="chart_area"><div style="width:<?php echo $count/$max_world_active_count*100?>%"></div></div>
					</td>
			</tr><?php $i++;if($i==10)break;endforeach;endif;?>
      </table>
		</div>
		<div id="panel_new_devices" class="panel">
			<table>
				<thead>
					<th width="150">国家</th>
					<th width="150">数量</th>
					<th>比例</th>
				</thead>
       <?php if($all_new_count): $i=0;foreach ($world_new_counts as $area_id=>$count):?><tr>
					<td><?php echo Area::nameof($area_id)?></td>
				  <td><?php echo $count?></td>
					<td class='percent'>
						<div class="label"><?php printf('%.02f', $count/$all_new_count*100)?>%</div>
						<div class="chart_area"><div style="width:<?php echo $count/$max_world_new_count*100?>%"></div></div>
					</td>
			</tr><?php $i++;if($i==10)break;endforeach;endif?>
      </table>
		</div>
	</div>
	<div style="margin:5px auto;width:99%;">注：多日的数量计算方式为每日相加，对活跃设备数来说，并不是真实数值</div>
</div>




<div class="block">
	<h3>TOP 20 省份分布<span class='right'><?php echo $from?> ~ <?php echo $to?></span></h3>
	<ul class="tabs">
		<li id="tab_active_devices" class="tab current">活跃设备</li>
		<li id="tab_new_devices" class="tab">新增设备</li>
	</ul>
	<div class="panels">
		<div id="panel_active_devices" class="panel current" style="height:auto;">
			<table>
				<thead>
					<th width="150">省份</th>
					<th width="150">数量</th>
					<th>比例</th>
				</thead>
       <?php if($china_all_active_count):$i=0;foreach ($province_active_counts as $area_id=>$count):?><tr>
					<td><?php echo Area::nameof($area_id)?></td>
				  <td><?php echo $count?></td>
					<td class='percent'>
						<div class="label"><?php printf('%.02f', $count/$china_all_active_count*100)?>%</div>
						<div class="chart_area"><div style="width:<?php echo $count/$max_province_active_count*100?>%"></div></div>
					</td>
			</tr><?php $i++;if($i==20)break;endforeach;endif?>
      </table>
		</div>
		<div id="panel_new_devices" class="panel" style="height:auto;">
			<table>
				<thead>
					<th width="150">省份</th>
					<th width="150">数量</th>
					<th>比例</th>
				</thead>
       <?php if($china_all_new_count): $i=0;foreach ($province_new_counts as $area_id=>$count):?><tr>
					<td><?php echo Area::nameof($area_id)?></td>
				  <td><?php echo $count?></td>
					<td class='percent'>
						<div class="label"><?php printf('%.02f', $count/$china_all_new_count*100)?>%</div>
						<div class="chart_area"><div style="width:<?php echo $count/$max_province_new_count*100?>%"></div></div>
					</td>
			</tr><?php $i++;if($i==20)break;endforeach;endif?>
      </table>
		</div>
	</div>
	<div style="margin:5px auto;width:99%;">注：多日的数量计算方式为每日相加，对活跃设备数来说，并不是真实数值</div>
</div>
