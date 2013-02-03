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
	$channel_condition = "1";
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



<div class="block">
	<h3>时段分析</h3>
	<ul class="tabs">
		<li id="tab_hourly_new_devices" class="tab current">新增设备</li>
		<li id="tab_hourly_active_devices" class="tab">活跃设备</li>
		<?php if($user->isAdmin()):?>
			<li id="tab_hourly_open_times" class="tab">启动次数</li>
		<?php endif?>
	</ul>
	<div class="panels">
		<div id="panel_hourly_new_devices" class="panel current">a</div>
		<div id="panel_hourly_active_devices" class="panel">b</div>
		<?php if($user->isAdmin()):?>
			<div id="panel_hourly_open_times" class="panel">c</div>
		<?php endif?>
	</div>
</div>


<div class="block">
	<h3>趋势分析</h3>
	<ul class="tabs">
		<li id="" class="tab current">新增设备</li>
		<li id="tab_daily_all_devices" class="tab">累计设备</li>
		<li id="tab_daily_active_devices" class="tab">活跃设备</li>
		<?php if($user->isAdmin()):?>
		<li id="tab_daily_open_times" class="tab">启动次数</li>
		<li id="tab_daily_senconds_spent_per_open" class="tab">平均每次使用时长</li>
		<?php endif?>
	</ul>
	<div class="panels">
		<div id="panel_daily_new_devices" class="panel current">a</div>
		<div id="panel_daily_all_devices" class="panel">b</div>
		<div id="panel_daily_active_devices" class="panel">c</div>
		<?php if($user->isAdmin()):?>
		<div id="panel_daily_open_times" class="panel">d</div>
		<div id="panel_daily_senconds_spent_per_open" class="panel">e</div>
		<?php endif;?>
	</div>
</div>

<?php if($user->isAdmin()){
	include "block_top_ten_version.php";
	include "block_top_ten_channel.php";
}?>


<script>
$(function(){
	render_chart('panel_hourly_new_devices','',root_url+'reports/AjaxHourlyNewDevices', {}, false,
			{tooltip: {formatter: function() { return parseInt(this.x,10) +':00 ~' + (parseInt(this.x,10) + 1) + ':00 新增 '+ this.y + ' 台设备';}} } );
	$("#tab_hourly_active_devices").click(function(){
		$("#panel_hourly_active_devices").show();
		render_chart('panel_hourly_active_devices','',root_url+'reports/AjaxHourlyActiveDevices', {}, false,
				{tooltip: {formatter: function() { return parseInt(this.x,10) +':00 ~' + (parseInt(this.x,10) + 1) + ':00 活跃 '+ this.y + ' 台设备';}} } );
	});
	$("#tab_hourly_open_times").click(function(){
		$("#panel_hourly_open_times").show();
		render_chart('panel_hourly_open_times','',root_url+'reports/AjaxHourlyOpenTimes', {}, false,
				{tooltip: {formatter: function() { return parseInt(this.x,10) +':00 ~' + (parseInt(this.x,10) + 1) + ':00 启动 '+ this.y + ' 次';}} } );
	});



	render_chart('panel_daily_new_devices','',root_url+'reports/AjaxDailyNewDevices', {}, false,
			{tooltip: {formatter: function() { return this.x + ': 新增 '+ this.y + ' 台设备';}} } );
	$("#tab_daily_all_devices").click(function(){
		$("#panel_daily_all_devices").show();
		render_chart('panel_daily_all_devices','',root_url+'reports/AjaxDailyAllDevices', {}, false,{
			tooltip: {formatter: function() { return this.x + ': 累计 '+ this.y + ' 台设备';}},
			chart: {defaultSeriesType: 'column'}
		} );
	});
	$("#tab_daily_active_devices").click(function(){
		$("#panel_daily_active_devices").show();
		render_chart('panel_daily_active_devices','',root_url+'reports/AjaxDailyActiveDevices', {}, false,{
			tooltip: {formatter: function() { return this.series.name + this.x +': '+ this.y;}},
			chart: {defaultSeriesType: 'column'},
			plotOptions: { column: {stacking: 'normal'} }
		} );
	});
	$("#tab_daily_open_times").click(function(){
		$("#panel_daily_open_times").show();
		render_chart('panel_daily_open_times','',root_url+'reports/AjaxDailyOpenTimes', {}, false,{
			tooltip: {formatter: function() { return this.x + ': 启动 '+ this.y + ' 次';}}, });
	});
	$("#tab_daily_senconds_spent_per_open").click(function(){
		$("#panel_daily_senconds_spent_per_open").show();
		render_chart('panel_daily_senconds_spent_per_open','',root_url+'reports/AjaxDailySecondsSpentPerOpen', {}, false,{
			tooltip: {formatter: function() { return this.x + ': 平均每次使用 '+ this.y + ' 秒';}}, });
	});
});
</script>

