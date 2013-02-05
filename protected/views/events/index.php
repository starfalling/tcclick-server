<h1>客户端自定义事件</h1>
<div class="block">
	<h3>全部事件</h3>
	<table>
		<thead><tr>
			<th style='width:240px'>名称</th>
			<th>别称</th>
			<th>消息数</th>
			<th style='width:140px'>操作</th>
		</tr></thead>
		<tbody>
			<?php foreach(Event::all() as $i=>$event):?><tr>
				<td><a href='<?php echo TCClick::app()->root_url, 'events/', $event->id?>'>
				<?php echo EventName::nameof($event->name_id)?></a></td>
				<td><?php echo EventName::nameof($event->alias_id)?></td>
				<td><?php $sql = "select sum(count) from {counter_daily_events} where event_id={$event->id}";
				echo TCClick::app()->db->query($sql)->fetchColumn(0);
				?></td>
				<td style='text-align:center'>
					<a href='<?php echo TCClick::app()->root_url, 'events/', $event->id?>'>查看</a>
					&nbsp;&nbsp;&nbsp;
					<a href='javascript:void(0)'>编辑</a>
					&nbsp;&nbsp;&nbsp;
					<a href='javascript:void(0)'>重置</a>
				</td>
			</tr><?php endforeach?>
		</tbody>
	</table>
</div>
