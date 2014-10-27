<?php
$version_ids = array();
$today = date("Y-m-d");
$yesterday = date("Y-m-d", time()-86400);
$today_new = array(); $yesterday_new = array();
$sql = "select * from {counter_daily_new_version} where date in ('$today', '$yesterday')";
foreach(TCClick::app()->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row){
	if($row['date'] == $today){
		$today_new[$row['version_id']] = $row['count'];
	}else{
		$yesterday_new[$row['version_id']] = $row['count'];
	}
}

$today_active = array(); $yesterday_active = array();
$sql = "select * from {counter_daily_active_version} where date in ('$today', '$yesterday')
order by `count` desc";
foreach(TCClick::app()->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row){
	if($row['date'] == $today){
		$today_active[$row['version_id']] = $row['count'];
	}else{
		$yesterday_active[$row['version_id']] = $row['count'];
		if(count($version_ids) < 10) $version_ids[] = $row['version_id'];
	}
}
?>
<div class="block">
	<h3>Top 10 版本</h3>
	<table>
		<thead>
			<tr>
				<th>版本</th>
				<th>昨日新增</th>
				<th>今日新增</th>
				<th>昨日活跃</th>
				<th>今日活跃</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($version_ids as $i=>$id):?>
			<tr class="<?php echo $i%2===0 ? "odd" : "even"?>">
				<td><?php echo Version::nameOf($id)?></td>
				<td><?php echo $yesterday_new[$id] ? $yesterday_new[$id] : 0?></td>
				<td><?php echo $today_new[$id] ? $today_new[$id] : 0?></td>
				<td><?php echo $yesterday_active[$id] ? $yesterday_active[$id] : 0?></td>
				<td><?php echo $today_active[$id] ? $today_active[$id] : 0?></td>
			</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</div>