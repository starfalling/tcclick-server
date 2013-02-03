<form method="post">
	<div class="row">
		<label>账户名：</label>
		<input name="username" size="30" value='<?php echo $user->username?>'/>
	</div>
	<div class="row">
		<label>密码：</label>
		<input name="password" size="30" type="password"/>
	</div>
	<div class="row">
		<label>渠道：</label>
		<input name="channels" size="30" value='<?php
		foreach($user->getChannelIds() as $channel_id) echo Channel::nameOf($channel_id), ",";
		?>'/>可输入多个渠道，渠道之间使用英文逗号隔开
	</div>
	<div class="row submit">
		<input type="submit" value="提交"/>
	</div>
</form>
<style>form .row label{width:100px;}</style>