<h1>外部站管理</h1>
<div class="block" style="padding:10px 20px">
  <a href='<?php echo TCClick::app()->root_url, 'externalSites/create' ?>'>创建外部站</a>
  <p>您可以通过外部访问码添加其他 tcclick 站，以便将多个 tcclick 统计站集中在该后台一起进行访问。</p>
  <table>
    <thead>
    <tr>
      <th style='width:40px'>ID</th>
      <th>站点</th>
      <th style='width:100px'>所属用户</th>
      <th style='width:40px'>权重</th>
      <th style='width:80px'>共同用户</th>
      <th style='width:100px'>操作</th>
      <th style='width:160px'>创建时间</th>
    </tr>
    </thead>
    <tbody><?php foreach($sites as $i => $site): ?>
      <tr>
        <td><?php echo $site->id ?></td>
        <td>
          <a href="<?php echo $site->url ?>" target='_blank'><?php echo $site->name ?></a>
          &nbsp;&nbsp;(<?php echo $site->is_admin ? '管理员' : '渠道账号' ?>)
        </td>
        <td style='text-align:center;padding:0;'><?php echo $site->getUser()->username ?></td>
        <td style='text-align:center;padding:0;'><?php echo $site->weight ?></td>
        <td style='text-align:center;padding:0;'><?php echo $site->calculate_mutual_devices ? '计算' : '--' ?></td>
        <td style='text-align:center;padding:0;'>
          <a href='<?php echo TCClick::app()->root_url, 'externalSites/', $site->id, '/update' ?>'>编辑</a>
          &nbsp;&nbsp;
          <?php if($site->status == 0): ?>
            <a href='javascript:void(0)' id='<?php echo $site->id ?>' class='delete'>删除</a>
          <?php else: ?>
            <a href='javascript:void(0)' id='<?php echo $site->id ?>' class='recover' style='color:red;'>恢复</a>
          <?php endif; ?>
        </td>
        <td><?php echo $site->created_at ?></td>
      </tr>
    <?php endforeach ?></tbody>
  </table>
</div>

<script>$(function() {
    $('a.delete').click(function() {
      var form = "<form action='<?php echo TCClick::app()->root_url?>externalSites/delete' method='post'>";
      form += "<input name='id' value='" + $(this).attr('id') + "' />";
      form += "</form>";
      $(form).submit();
    });
    $('a.recover').click(function() {
      var form = "<form action='<?php echo TCClick::app()->root_url?>externalSites/recover' method='post'>";
      form += "<input name='id' value='" + $(this).attr('id') + "' />";
      form += "</form>";
      $(form).submit();
    });
  });</script>