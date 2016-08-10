<h1>
  <?php
  echo EventName::nameof($event->name_id);
  $now = time();
  echo TCClickUtil::selector(array(
    array("label" => "最近一月", "from" => date("Y-m-d", $now - 86400 * 30), "to" => null),
    array("label" => "最近两月", "from" => date("Y-m-d", $now - 86400 * 60), "to" => null),
    array("label" => "最近三月", "from" => date("Y-m-d", $now - 86400 * 90), "to" => null),
    array("label" => "最近一年", "from" => date("Y-m-d", $now - 86400 * 365), "to" => null),
  ), array('from' => date("Y-m-d", $now - 86400 * 30)));

  $sql = "select * from {event_params} where event_id ={$_GET['id']}";
  $stmt = TCClick::app()->db->query($sql);
  $param_array = array();
  foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $param_array[] = array("label" => EventName::nameof($row['name_id']), "param_id" => $row['param_id']);
  }
  echo TCClickUtil::selector($param_array);
  $default_param_id = $param_array[0]['param_id'];


  $sql = "select * from {versions} order by id DESC";
  $stmt = TCClick::app()->db->query($sql);
  $version_array = array();
  $version_array[] = array("label" => "全部版本", "version_id" => "0");
  foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $version_array[] = array("label" => $row['version'], "version_id" => $row['id']);
  }
  echo TCClickUtil::selector($version_array);
  ?>
</h1>
<div class="block">
  <h3><a href="<?php echo TCClick::app()->root_url ?>events">&lt;&lt; 返回事件列表</a></h3>
  <ul class="tabs">
    <li id="tab_daily_spline" class="tab current">次数分布</li>
    <li id="tab_daily_area" class="tab">次数率分布</li>
  </ul>
  <div class="panels">
    <div id="panel_daily_spline" class="panel current">b</div>
    <div id="panel_daily_area" class="panel">a</div>
  </div>
</div>
<script>
  $(function() {
    render_chart('panel_daily_spline', '',
      root_url + 'events/AjaxDailyCountsSpline?event_id=<?php echo $_GET['id']?>&param_id=<?php echo $default_param_id?>',
      {}, false, {
        tooltip: {
          formatter: function() {
            return this.series.name + ':' + this.x + ': ' + this.y;
          }
        },
      });

    $("#tab_daily_area").click(function() {
      $("#panel_daily_area").show();
      render_chart('panel_daily_area', '',
        root_url + 'events/AjaxDailyCounts?event_id=<?php echo $_GET['id']?>&param_id=<?php echo $default_param_id?>',
        {}, false, {
          tooltip: {
            formatter: function() {
              return this.series.name + ':' + this.x + ': ' + Math.round(this.y * 100) + '%';
            }
          },
          chart: {defaultSeriesType: 'area'},
          xAxis: {
            labels: {rotation: -50, align: "right"},
            tickmarkPlacement: 'on',
          },
          yAxis: {
            labels: {
              formatter: function() {
                return this.value * 100 + '%';
              }
            },
          },
          plotOptions: {
            area: {
              stacking: 'normal',
              lineWidth: 0,
              marker: {
                enabled: false,
                symbol: 'circle',
                radius: 2,
                states: {
                  hover: {
                    enabled: true
                  }
                }
              }
            }
          },
        });
    });
  });

</script>