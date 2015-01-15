<h1>版本分布
<?php
$now = time();
echo TCClickUtil::selector(array(
		array("label"=>"昨天", "from"=>date("Y-m-d", $now-86400), "to"=>date("Y-m-d", $now-86400)),
		array("label"=>"前天", "from"=>date("Y-m-d", $now-86400*2), "to"=>date("Y-m-d", $now-86400*2)),
		array("label"=>"最近一月", "from"=>date("Y-m-d", $now-86400*30), "to"=>null),
		array("label"=>"最近两月", "from"=>date("Y-m-d", $now-86400*60), "to"=>null),
		array("label"=>"最近三月", "from"=>date("Y-m-d", $now-86400*90), "to"=>null),
		array("label"=>"最近一年", "from"=>date("Y-m-d", $now-86400*365), "to"=>null),
))?></h1>

<div class="block">
	<h3>趋势</h3>
	<ul class="tabs">
		<li id="tab_daily_new_devices" class="tab current">新增设备</li>
		<li id="tab_daily_active_devices" class="tab">活跃设备</li>
		<li id="tab_daily_update_devices" class="tab">版本升级</li>
	</ul>
	<div class="panels">
		<div id="panel_daily_new_devices" class="panel current">a</div>
		<div id="panel_daily_active_devices" class="panel">b</div>
		<div id="panel_daily_update_devices" class="panel">b</div>
	</div>
</div>


<script>
$(function(){
	render_chart('panel_daily_new_devices','',root_url+'reportsVersion/AjaxDailyNewDevices', {}, false,
			{tooltip: {formatter: function() { return this.x + ': 新增 '+ this.y + ' 台设备';}},
		 } );
	$("#tab_daily_active_devices").click(function(){
		$("#panel_daily_active_devices").show();
		render_chart('panel_daily_active_devices','',root_url+'reportsVersion/AjaxDailyActiveDevices', {}, false,
			{tooltip: {formatter: function() { return this.x + ': 活跃 '+ this.y + ' 台设备';}} } );
	});
	$("#tab_daily_update_devices").click(function(){
		$("#panel_daily_update_devices").show();
		render_chart('panel_daily_update_devices','',root_url+'reportsVersion/AjaxDailyUpdateDevices', {}, false,
			{tooltip: {formatter: function() { return this.x + ': 升级 '+ this.y + ' 台设备';}} } );
	});
});
</script>