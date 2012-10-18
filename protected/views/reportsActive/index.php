<h1>活跃设备
<?php echo TCClickUtil::selector(array(
		array("label"=>"最近一月", "from"=>date("Y-m-d", time()-86400*30)),
		array("label"=>"最近两月", "from"=>date("Y-m-d", time()-86400*60)),
		array("label"=>"最近三月", "from"=>date("Y-m-d", time()-86400*90)),
		array("label"=>"最近一年", "from"=>date("Y-m-d", time()-86400*365)),
))?></h1>
<div class="block">
  <h3>活跃设备趋势</h3>
  <ul class="tabs">
    <li id="" class="tab current">日活跃设备</li>
    <li id="tab_active_week" class="tab">周活跃设备</li>
    <li id="tab_active_month" class="tab">月活跃设备</li>
    <li id="tab_active_week_rate" class="tab">周活跃率</li>
    <li id="tab_active_month_rate" class="tab">月活跃率</li>
  </ul> 
  <div class="panels">
    <div id="panel_active_day" class="panel current">a</div>
    <div id="panel_active_week" class="panel">b</div>
    <div id="panel_active_month" class="panel">c</div>
    <div id="panel_active_week_rate" class="panel">d</div>
    <div id="panel_active_month_rate" class="panel">f</div>
  </div>
</div>
<script type="text/javascript">
<!--
$(function(){
	//日活跃设备
	render_chart('panel_active_day','',root_url+'reportsActive/AjaxActiveDaily', {}, false,
			{tooltip: {formatter: function() { return this.x+ '活跃 '  + this.y + ' 台设备';}} } );

	 //上周活跃设备
	$("#tab_active_week").click(function(){
		$("#panel_active_week").show();
		render_chart('panel_active_week','',root_url+'reportsActive/AjaxActiveWeekly', {}, false,
				{tooltip: {formatter: function() { return this.x+ '后一周活跃 '  + this.y + ' 台设备';}} } );
		});
	//上月活跃设备
	$("#tab_active_month").click(function(){
		 $("#panel_active_month").show();
		 render_chart('panel_active_month', '', root_url+'reportsActive/AjaxActiveMonthly',{},false,
				{tooltip: {formatter:function(){ return this.x + '活跃 ' +this.y + ' 台设备'}},
			 xAxis: {labels:{ setp: 2, rotation: -35, align: "right"}}}); 
		});
	//上周活跃率
	$("#tab_active_week_rate") .click(function(){
		  $("#panel_active_week_rate").show();
		  render_chart('panel_active_week_rate', '', root_url+'reportsActive/AjaxActiveWeekRate', {}, false, 
				  {tooltip:{formatter:function(){return '全部设备  '+this.x + ' : ' +this.y+ '%'}}});
		});
	//上个月的活跃率
	$("#tab_active_month_rate").click(function(){
		  $("#panel_active_month_rate").show();
		  render_chart('panel_active_month_rate', '', root_url+'reportsActive/AjaxActiveMonthRate', {}, false, 
				  {tooltip:{formatter:function(){return '全部设备 '+this.x+ ' : ' + this.y+ '%'}}});
		});
});



//-->
</script>