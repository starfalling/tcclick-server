<h1>渠道分布
<?php
$now = time();
echo TCClickUtil::selector(array(
		array("label"=>"最近一月", "from"=>date("Y-m-d", $now-86400*30), "to"=>null),
		array("label"=>"最近两月", "from"=>date("Y-m-d", $now-86400*60), "to"=>null),
		array("label"=>"最近三月", "from"=>date("Y-m-d", $now-86400*90), "to"=>null),
		array("label"=>"最近一年", "from"=>date("Y-m-d", $now-86400*365), "to"=>null),
), array('from'=>date("Y-m-d", $now-86400*30)))?></h1>

<div class="block">
	<h3>时段分析</h3>
	<ul class="tabs">
		<li id="tab_hourly_new_devices_today" class="tab current">今日新增</li>
		<li id="tab_hourly_active_devices_today" class="tab">今日活跃</li>
		<li id="tab_hourly_new_devices_yesterday" class="tab">昨日新增</li>
		<li id="tab_hourly_active_devices_yesterday" class="tab">昨日活跃</li>
	</ul>
	<div class="panels">
		<div id="panel_hourly_new_devices_today" class="panel current">a</div>
		<div id="panel_hourly_active_devices_today" class="panel">b</div>
		<div id="panel_hourly_new_devices_yesterday" class="panel">c</div>
		<div id="panel_hourly_active_devices_yesterday" class="panel">d</div>
	</div>
</div>

<div class="block">
	<h3>趋势</h3>
	<ul class="tabs">
		<li id="tab_daily_new_devices" class="tab current">新增设备</li>
		<li id="tab_daily_active_devices" class="tab">活跃设备</li>
	</ul>
	<div class="panels">
		<div id="panel_daily_new_devices" class="panel current">a</div>
		<div id="panel_daily_active_devices" class="panel">b</div>
	</div>
</div>


<script>
$(function(){
	var today = '<?php echo date("Y-m-d")?>';
	var yesterday = '<?php echo date("Y-m-d", time()-86400)?>';
	render_chart('panel_hourly_new_devices_today','',root_url+'reportsChannel/AjaxHourlyNewDevices', {date:today}, false,
			{tooltip: {formatter: function() { return this.x + ': 新增 '+ this.y + ' 台设备';}} } );
	$("#tab_hourly_active_devices_today").click(function(){
		$("#panel_hourly_active_devices_today").show();
		render_chart('panel_hourly_active_devices_today','',root_url+'reportsChannel/AjaxHourlyActiveDevices', {date:today}, false,
			{tooltip: {formatter: function() { return this.x + ': 活跃 '+ this.y + ' 台设备';}} } );
	});
	$("#tab_hourly_new_devices_yesterday").click(function(){
		$("#panel_hourly_new_devices_yesterday").show();
		render_chart('panel_hourly_new_devices_yesterday','',root_url+'reportsChannel/AjaxHourlyNewDevices', {date:yesterday}, false,
			{tooltip: {formatter: function() { return this.x + ': 活跃 '+ this.y + ' 台设备';}} } );
	});
	$("#tab_hourly_active_devices_yesterday").click(function(){
		$("#panel_hourly_active_devices_yesterday").show();
		render_chart('panel_hourly_active_devices_yesterday','',root_url+'reportsChannel/AjaxHourlyActiveDevices', {date:yesterday}, false,
			{tooltip: {formatter: function() { return this.x + ': 活跃 '+ this.y + ' 台设备';}} } );
	});

	render_chart('panel_daily_new_devices','',root_url+'reportsChannel/AjaxDailyNewDevices', {}, false,
			{tooltip: {formatter: function() { return this.x + ': 新增 '+ this.y + ' 台设备';}} } );
	$("#tab_daily_active_devices").click(function(){
		$("#panel_daily_active_devices").show();
		render_chart('panel_daily_active_devices','',root_url+'reportsChannel/AjaxDailyActiveDevices', {}, false,
			{tooltip: {formatter: function() { return this.x + ': 活跃 '+ this.y + ' 台设备';}} } );
	});
});
</script>