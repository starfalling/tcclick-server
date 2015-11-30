<?php $this->registerScriptFile("Highcharts-4.1.9/highcharts.js");?>
<?php $this->registerScriptFile("jquery.blockUI.js");?>
<?php $this->registerScriptFile("application.js");?>
<?php $this->registerScriptFile("dhtmlgoodies_calendar.js")?>
<?php $this->registerCssFile("style.css");?>
<?php $this->registerCssFile("dhtmlgoodies_calendar.css");?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <script type="text/javascript">var root_url = '<?php echo TCClick::app()->root_url?>';</script>
    <script type="text/javascript" src="<?php echo TCClick::app()->root_url ?>js/jquery-1.7.2.min.js" ></script>
    <?php if(!empty($this->style_files)):foreach($this->style_files as $style):?>
    <link type="text/css" rel="stylesheet" href="<?php echo $style?>" />
	  <?php endforeach;endif;?>
    <title><?php echo $this->title?></title>
  </head>
  <body>
  	<div id="page_header_wrapper"><div id="page_header">
  		<div class="user">
  			<?php $user = User::current(); if($user): echo $user->username?>
  				<a href="<?php echo TCClick::app()->root_url?>logout">退出登录</a>
  			<?php else:?>
  				<a href="<?php echo TCClick::app()->root_url?>login">登录</a>
  			<?php endif;?>
  		</div>
  		<div class="logo">tcclick</div>
  	</div></div>
  	<div id="page_content">
  		<div id="right_column">
  			<div class="message">
  				<?php if($this->info):?><div class='info'><?php echo $this->info?></div><?php endif?>
  				<?php if($this->error):?><div class='error'><?php echo $this->error?></div><?php endif?>
  			</div>
  			<?php echo $content?>
  		</div>
  		<?php include "menu.php" ?>
  	</div>
  	<?php include "footer.php"?>
  </body>
  <?php if(!empty($this->script_files)):foreach($this->script_files as $script):?>
  <script type="text/javascript" src="<?php echo $script?>" ></script>
  <?php endforeach;endif;?>
</html>
