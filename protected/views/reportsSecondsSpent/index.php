<h1>使用时长</h1>
<div align="right">
<form>
选择对比日期:<input value="<?php echo $_GET['compare_to']?>" id="compare_to"  name="compare_to">
<input class="tab" id="calendar_button" type="button" style="background-image: url('images/calendar_button.jpg');width: 25px; height: 25px";">
</form>
</div>

<div class="block">
	<h3>使用时长分布</h3>
	<ul class="tabs">
		<li id="tab_per_open" class="tab current">单次使用时长</li>
		<li id="tab_per_day" class="tab">日使用时长</li>
		<li id="tab_per_week" class="tab">周使用时长</li>
	</ul>
	<div class="panels">
		<div id="panel_per_open" class="panel current">a</div>
		<div id="panel_per_day" class="panel">b</div>
		<div id="panel_per_week" class="panel">  
<div id="datepicker"></div></div>
	</div>
</div>

<script>
$(function(){
	render_chart("panel_per_open",'',root_url+'reportsSecondsSpent/AjaxPerOpen', {compare_to:'<?php echo $_GET['compare_to']?>'}, false,{
		tooltip: {formatter: function() { return this.series.name +': '+ Math.round(this.y*1000)/10+ '%';} },
		chart: {defaultSeriesType: "bar"},
		xAxis: {labels: {rotation: 0}},
		yAxis: {labels: {formatter: function(){ return (this.value*100).toFixed(1) + "%"; }}},
		title: {text: '使用时长分布'}
	});

	$("#tab_per_day").click(function(){
		$("#panel_per_day").show();
		render_chart("panel_per_day",'',root_url+'reportsSecondsSpent/AjaxPerday', {compare_to:'<?php echo $_GET['compare_to']?>'}, false,{
			tooltip: {formatter: function() { return this.series.name +': '+ Math.round(this.y*1000)/10+ '%';} },
			chart: {defaultSeriesType: "bar"},
			xAxis: {labels: {rotation: 0}},
			yAxis: {labels: {formatter: function(){ return (this.value*100).toFixed(1)  + "%"; }}},
			title: {text: '使用时长分布'}
		});
	});

	$("#calendar_button").click(function(){
		displayCalendar(document.forms[0].compare_to,'yyyy-mm-dd',this);
		$("table tr").live("click",function() {
			$(this).find('td').ready(function(){
				 var compare_to = $("#compare_to").val();
				 window.location.href='reportsSecondsSpent?compare_to='+compare_to+''; 
				});
			});
		});
});
</script>
<style>.block .panels .panel{height:400px;}</style>