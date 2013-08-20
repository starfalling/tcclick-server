<h1>外部访问码</h1>
<div class="block" style="padding:10px 20px">
	<a id='create_external_code' href='javascript:void(0)'>创建访问码</a>
	<p>外部访问码用于把数据开放给其他 tcclick 站，以便将多个 tcclick 统计站集中在一起进行访问。</p>
	<table>
		<thead><tr>
			<th style='width:40px'>编号</th>
			<th>访问码</th>
			<th style='width:100px'>所属用户</th>
			<th style='width:100px'>操作</th>
			<th style='width:160px'>创建时间</th>
		</tr></thead>
		<tbody><?php foreach($codes as $i=>$code):?>
			<tr>
				<td><?php echo $code->id?></td>
				<td><?php echo $code->code?></td>
				<td style='text-align:center;padding:0;'><?php echo $code->getUser()->username?></td>
				<td style='text-align:center;padding:0;'>
					<a href='javascript:void(0)' id='<?php echo $code->id?>' class='delete'>删除</a>
				</td>
				<td><?php echo $code->created_at?></td>
			</tr>
		<?php endforeach?></tbody>
	</table>
</div>

<script>$(function(){
  $('a#create_external_code').click(function(){
    var form = "<form action='<?php echo TCClick::app()->root_url?>externalCodes/create' method='post'>";
    form += "</form>";
    $(form).submit();
  });
  $('a.delete').click(function(){
    var form = "<form action='<?php echo TCClick::app()->root_url?>externalCodes/delete' method='post'>";
    form += "<input name='id' value='"+$(this).attr('id')+"' />";
    form += "</form>";
    $(form).submit();
  });
});</script>