<h1>账号管理</h1>
<div class="block" style="padding:10px 20px">
	<a href='<?php echo TCClick::app()->root_url?>users/create'>创建账号</a>
	<table>
		<thead><tr>
			<th style='width:40px'>编号</th>
			<th>用户名</th>
			<th>开放渠道</th>
			<th style='width:60px'>状态</th>
			<th style='width:100px'>操作</th>
			<th style='width:160px'>创建时间</th>
		</tr></thead>
		<tbody>
			<?php foreach($users as $i=>$user):?><tr id='<?php echo $user->id?>'>
				<td><?php echo $user->id?></td>
				<td><?php echo $user->username?></td>
				<td><?php if($user->isAdmin()) echo '所有'; else{
					foreach($user->getChannelIds() as $channel_id){
						echo Channel::nameOf($channel_id), ",";
					}
				}?></td>
				<td><?php if($user->status == User::STATUS_BANNED){
					echo "<span class='banned'>已禁用</span>";
				}elseif($user->status == User::STATUS_NORMAL){
					echo "<span class='normal'>正常</span>";
				}?></td>
				<td><?php if(!$user->isAdmin()):?>
					<a href='<?php echo TCClick::app()->root_url, 'users/', $user->id, '/update'?>'>编辑</a>
					&nbsp;&nbsp;&nbsp;
					<?php if($user->status == User::STATUS_BANNED){
						echo "<a href='javascript:void(0)' class='banned'>恢复</span>";
					}elseif($user->status == User::STATUS_NORMAL){
						echo "<a href='javascript:void(0)' class='normal'>禁用</span>";
					}?>
				<?php endif?></td>
				<td><?php echo $user->created_at?></td>
			</tr><?php endforeach?>
		</tbody>
	</table>
</div>
<script>$(function(){
	$('td a.banned').click(function(){
		var user_id = $(this).parent().parent().attr('id');
		var form = "<form action='"+root_url+"users/"+user_id+"/recover' method='post'/>";
		$(form).submit();
	});
	$('td a.normal').click(function(){
		var user_id = $(this).parent().parent().attr('id');
		var form = "<form action='"+root_url+"users/"+user_id+"/ban' method='post'/>";
		$(form).submit();
	});
});</script>