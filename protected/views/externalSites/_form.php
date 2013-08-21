<form method="post">
	<div class="row">
		<label for='form_name'>统计站名称：</label>
		<input id='form_name' name="name" size="60" value='<?php echo $site->name?>'/>
	</div>
	<div class="row">
		<label for='form_code'>访问码：</label>
		<input id='form_code' name="code" size="60" value='<?php echo $site->code?>' placeholder='请输入40位外部访问码'/>
	</div>
	<div class="row">
		<label for='form_url'>URL地址：</label>
		<input id='form_url' name="url" size="60" value='<?php echo $site->url?>'/>
	</div>
	<div class="row submit">
		<input type="submit" value="提交"/>
	</div>
</form>
<style>form .row label{width:100px;}</style>