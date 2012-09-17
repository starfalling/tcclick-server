<?php
$channel_ids = array();
$today = date("Y-m-d");
$yesterday = date("Y-m-d", time()-86400);
$today_new = array(); $yesterday_new = array();
$sql = "select * from {counter_daily_new} where date in ('$today', '$yesterday')
and channel_id<>0 order by `count` desc";
foreach(TCClick::app()->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row){
	if($row['date'] == $today){
		$today_new[$row['channel_id']] = $row['count'];
	}else{
		$yesterday_new[$row['channel_id']] = $row['count'];
		if(count($channel_ids) < 10) $channel_ids[] = $row['channel_id'];
	}
}

$today_active = array(); $yesterday_active = array();
$sql = "select * from {counter_daily_active} where date in ('$today', '$yesterday')
and channel_id<>0";
foreach(TCClick::app()->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row){
	if($row['date'] == $today){
		$today_active[$row['channel_id']] = $row['count'];
	}else{
		$yesterday_active[$row['channel_id']] = $row['count'];
	}
}
?>
<div class="block">
	<h3>Top 10 渠道</h3>
	<table>
		<thead>
			<tr>
				<th>渠道</th>
				<th>昨日新增</th>
				<th>今日新增</th>
				<th>昨日活跃</th>
				<th>今日活跃</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($channel_ids as $i=>$id):?>
			<tr class="<?php echo $i%2===0 ? "odd" : "even"?>">
				<td><?php echo Channel::nameOf($id)?></td>
				<td><?php echo $yesterday_new[$id] ? $yesterday_new[$id] : 0?></td>
				<td><?php echo $today_new[$id] ? $today_new[$id] : 0?></td>
				<td><?php echo $yesterday_active[$id] ? $yesterday_active[$id] : 0?></td>
				<td><?php echo $today_active[$id] ? $today_active[$id] : 0?></td>
			</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</div>