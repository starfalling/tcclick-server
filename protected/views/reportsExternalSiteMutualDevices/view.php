<?php

/**
 * @var ExternalSite $external_site
 */

?>
<h1>与『<?php echo $external_site->name ?>』共有的设备情况
  <?php

  $now = time();
  echo TCClickUtil::selector(array(
    array("label" => "最近一月", "from" => date("Y-m-d", $now - 86400 * 30), "to" => null),
    array("label" => "最近两月", "from" => date("Y-m-d", $now - 86400 * 60), "to" => null),
    array("label" => "最近三月", "from" => date("Y-m-d", $now - 86400 * 90), "to" => null),
    array("label" => "最近一年", "from" => date("Y-m-d", $now - 86400 * 365), "to" => null),
  ), array('from' => date("Y-m-d", $now - 86400 * 30)));
  ?>
</h1>
<div class="block">
  <h3><a href="<?php echo TCClick::app()->root_url ?>reportsExternalSiteMutualDevices">&lt;&lt; 返回外部站列表</a></h3>
  <ul class="tabs">
    <li id="tab_daily_new" class="tab current">新增设备</li>
    <li id="tab_daily_active" class="tab">活跃设备</li>
  </ul>
  <div class="panels">
    <div id="panel_daily_new" class="panel current">b</div>
    <div id="panel_daily_active" class="panel">a</div>
  </div>
</div>
<script>
  $(function() {
    render_chart('panel_daily_new', '',
      root_url + 'ReportsExternalSiteMutualDevices/AjaxDailyCountsSpline?type=new&site_id=<?php echo $external_site->id?>',
      {}, false, {
        tooltip: {
          formatter: function() {
            return this.series.name + ':' + this.x + ': ' + this.y;
          }
        },
      });

    $("#tab_daily_active").click(function() {
      $("#panel_daily_active").show();
      render_chart('panel_daily_active', '',
        root_url + 'ReportsExternalSiteMutualDevices/AjaxDailyCountsSpline?type=active&site_id=<?php echo $external_site->id?>',
        {}, false, {
          tooltip: {
            formatter: function() {
              return this.series.name + ':' + this.x + ': ' + this.y;
            }
          },
        });
    });
  });

</script>