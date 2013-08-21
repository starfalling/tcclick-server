<?php
$today = date("Y-m-d");
$yesterday = date("Y-m-d", time()-86400);
$yesterday_row = array("new_devices_count"=>0, "seconds_per_open"=>0,
		"active_devices_count"=>0, "open_times"=>0, "update_devices_count"=>0);
$today_row = array("new_devices_count"=>0, "seconds_per_open"=>0,
		"active_devices_count"=>0, "open_times"=>0, "update_devices_count"=>0);
$data = array($yesterday=>&$yesterday_row, $today=>&$today_row);
$all_devices_count = 0;

$user = User::current();
$channel_ids = $user->getChannelIds();
$channel_condition = "0";
if($user->isAdmin()){ 
	$channel_condition = "channel_id=0";
	$today_row['seconds_per_open'] = TCClickCounter::calculateSecondsSpentPerOpen($today);
	$yesterday_row['seconds_per_open'] = TCClickCounter::calculateSecondsSpentPerOpen($yesterday);
	$sql = 'select count(*) from {devices}';
	$all_devices_count = TCClick::app()->db->query($sql)->fetchColumn();
}elseif($channel_ids){
	$channel_condition = ' channel_id in (' . join(',', $channel_ids) . ')';
	$sql = "select sum(count) from {counter_daily_new} where $channel_condition";
	$all_devices_count = TCClick::app()->db->query($sql)->fetchColumn();
}

$sql = "select date, sum(count) as c from {counter_daily_new} where $channel_condition
and date in ('{$yesterday}', '{$today}') group by date"; // 日新增
foreach(TCClick::app()->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row){
	$data[$row['date']]['new_devices_count'] = $row['c'];
}

$sql = "select date, sum(count) as c from {counter_daily_active} where $channel_condition
and date in ('{$yesterday}', '{$today}') group by date"; // 日活跃
foreach(TCClick::app()->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row){
	$data[$row['date']]['active_devices_count'] = $row['c'];
}

$sql = "select date, sum(count) as c from {counter_daily_update} where $channel_condition
and date in ('{$yesterday}', '{$today}') group by date"; // 日升级
foreach(TCClick::app()->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row){
	$data[$row['date']]['update_devices_count'] = $row['c'];
}
?><div class="summary">
	<div class="pro_block">
		<h3 class="title">活跃用户</h3>
		<hr size="1" />
		<div>
			<span class="date">昨日</span>
			<span class="number"><?php echo $yesterday_row['active_devices_count']?></span>
		</div>
		<div>
			<span class="date">今日</span>
			<span class="number"><?php echo $today_row['active_devices_count']?></span>
		</div>
	</div>
	<div class="pro_block">
		<h3 class="title">新增用户</h3>
		<hr size="1" />
		<div>
			<span class="date">昨日</span>
			<span class="number"><?php echo $yesterday_row['new_devices_count']?></span>
		</div>
		<div>
			<span class="date">今日</span>
			<span class="number"><?php echo $today_row['new_devices_count']?></span>
		</div>
	</div>
	<div class="pro_block">
		<h3 class="title">升级用户</h3>
		<hr size="1" />
		<div>
			<span class="date">昨日</span>
			<span class="number"><?php echo $yesterday_row['update_devices_count']?></span>
		</div>
		<div>
			<span class="date">今日</span>
			<span class="number"><?php echo $today_row['update_devices_count']?></span>
		</div>
	</div>
	<div class="pro_block">
		<h3 class="title">平均每次使用时长</h3>
		<hr size="1" />
		<div>
			<span class="date">昨日</span>
			<span class="number"><?php echo TCClickUtil::formatSecondsSpent($yesterday_row['seconds_per_open'])?></span>
		</div>
		<div>
			<span class="date">今日</span>
			<span class="number"><?php echo TCClickUtil::formatSecondsSpent($today_row['seconds_per_open'])?></span>
		</div>
	</div>
	<div class="pro_block">
		<h3 class="title">总设备数</h3>
		<hr size="1" />
		<div class='all_devices_count'>
			<span class="number"><?php echo $all_devices_count?></span>
		</div>
	</div>
</div>