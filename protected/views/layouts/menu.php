<div id="page_menu">
	<dl>
		<dt>统计概况<b></b></dt>
		<dd><a href="<?php echo TCClick::app()->root_url?>reports">基本统计</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url?>reportsVersion">版本分布</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url?>reportsChannel">渠道分布</a></dd>
	</dl>
	<dl>
		<dt>用户分析<b></b></dt>
		<dd><a href="<?php echo TCClick::app()->root_url?>reportsActive">活跃设备</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url?>reportsRetention">留存设备</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url?>reportsLaunch">使用频率</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url?>reportsSecondsSpent">使用时长</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url?>reportsAreas">地域</a></dd>
	</dl>
	<dl>
		<dt>终端及网络<b></b></dt>
		<dd><a href="<?php echo TCClick::app()->root_url?>reportsModels">设备</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url?>reportsOsVersion">操作系统</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url?>reportsResolution">分辨率</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url?>reportsCarrier">运营商</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url?>reportsNetWorks">联网方式</a></dd>
	</dl>
	<dl>
		<dt>错误分析<b></b></dt>
		<dd><a href="<?php echo TCClick::app()->root_url?>exceptions">错误分析</a></dd>
	</dl>
	<?php if(User::current()):?>
	<dl>
		<dt>账号<b></b></dt>
		<?php if(User::current()->username=="admin"):?>
		<dd><a href="<?php echo TCClick::app()->root_url?>users">账号管理</a></dd>
		<?php endif?>
		<dd><a href="<?php echo TCClick::app()->root_url?>users/changePassword">修改密码</a></dd>
	</dl>
	<?php endif;?>
</div>