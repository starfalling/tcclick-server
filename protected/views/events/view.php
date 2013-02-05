<h1><?php echo EventName::nameof($event->name_id)?><?php echo TCClickUtil::selector(array(
		array("label"=>"最近一月", "from"=>date("Y-m-d", time()-86400*30)),
		array("label"=>"最近两月", "from"=>date("Y-m-d", time()-86400*60)),
		array("label"=>"最近三月", "from"=>date("Y-m-d", time()-86400*90)),
		array("label"=>"最近一年", "from"=>date("Y-m-d", time()-86400*365)),
));
echo TCClickUtil::selector(array(
		array("label"=>"全部版本"),
))?></h1>
<div class="block">
	<h3><a href="<?php echo TCClick::app()->root_url?>events">&lt;&lt; 返回事件列表</a></h3>
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