<h1>编辑账号 - <?php echo $user->username?></h1>
<div class="block">
	<h3><a href="<?php echo TCClick::app()->root_url?>users">&lt;&lt; 返回账号管理</a></h3>
	<?php $this->renderPartial('_form', array('user'=>$user))?>
</div>
