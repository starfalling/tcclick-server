<div class="block">
	<h3>明细</h3>
	<table class='exceptions_list'>
		<thead>
			<tr>
				<th style="width:30px;"><input class='check_all' type='checkbox'/></th>
				<th>摘要</th>
				<th style="width:60px;">应用版本</th>
				<th style="width:100px;">最后出现</th>
				<th style="width:80px;">次数</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($rows as $i=>$row):?><tr id="<?php echo $row['id']?>" class="<?php echo $i%2===0 ? "odd" : "even"?>">
				<td><input class='checkbox' type='checkbox'/></td>
				<td class='title'><a href='<?php echo TCClick::app()->root_url, 'exceptions/', $row['id']?>'>
				<?php echo substr($row['exception'], 0, strpos($row['exception'], "\n"))?></a></td>
				<td class='app_version'><?php echo Version::nameOf($row['version_id'])?></td>
				<td class='updated_at'><?php echo TCClickUtil::readableDate($row['updated_at'])?></td>
				<td class='count'><?php echo $row['count']?></td>
			</tr><?php endforeach?>
		</tbody>
	</table>
	<?php TCClickUtil::pager($pages_count, $current_page)?>
</div>
