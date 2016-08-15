<?php 
$user = User::current(); 
$external_site_id = $_GET['external_site_id'] ? '?external_site_id='.$_GET['external_site_id'] : '';
$sites = ExternalSite::allForCurrentUser();
?>
<?php if(!empty($sites)):
	$selector_params = array(array("label"=>"主站", "external_site_id"=>null));
	foreach($sites as $site) $selector_params[] = array("label"=>$site->name, "external_site_id"=>$site->id);?>
	<div id='site_selector'><?php echo TCClickUtil::selector($selector_params)?></div>
<?php endif?>
<div id="page_menu">
	<dl>
		<dt>统计概况<b></b></dt>
		<dd><a href="<?php echo TCClick::app()->root_url, 'reports', $external_site_id?>">基本统计</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url, 'reportsChannel', $external_site_id?>">渠道分布</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url, 'reportsGooglePlayReferrer', $external_site_id?>">子渠道分布(安卓)</a></dd>
		<?php if($user && $user->isAdmin()):?>
		<dd><a href="<?php echo TCClick::app()->root_url, 'reportsExternalSiteMutualDevices', $external_site_id?>">外部站共有设备</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url, 'reportsVersion', $external_site_id?>">版本分布</a></dd>
		<?php endif?>
	</dl>
	<?php if($user && $user->isAdmin()):?>
	<dl>
		<dt>用户分析<b></b></dt>
		<dd><a href="<?php echo TCClick::app()->root_url, 'reportsActive', $external_site_id?>">活跃设备</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url, 'reportsRetention', $external_site_id?>">留存设备</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url, 'reportsLaunch', $external_site_id?>">使用频率</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url, 'reportsSecondsSpent', $external_site_id?>">使用时长</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url, 'reportsAreas', $external_site_id?>">地域</a></dd>
	</dl>
	<dl>
		<dt>客户端事件<b></b></dt>
		<dd><a href="<?php echo TCClick::app()->root_url, 'events', $external_site_id?>">事件列表</a></dd>
	</dl>
	<dl>
		<dt>终端及网络<b></b></dt>
		<dd><a href="<?php echo TCClick::app()->root_url, 'reportsModels', $external_site_id?>">设备</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url, 'reportsOsVersion', $external_site_id?>">操作系统</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url, 'reportsResolution', $external_site_id?>">分辨率</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url, 'reportsCarrier', $external_site_id?>">运营商</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url, 'reportsNetWorks', $external_site_id?>">联网方式</a></dd>
	</dl>
	<dl>
		<dt>错误分析<b></b></dt>
		<dd><a href="<?php echo TCClick::app()->root_url, 'exceptions', $external_site_id?>">错误分析</a></dd>
	</dl>
	<?php endif?>
	<?php if($user && !$external_site_id):?>
	<dl>
		<dt>账号<b></b></dt>
		<?php if(User::current()->username=="admin"):?>
		<dd><a href="<?php echo TCClick::app()->root_url, 'users'?>">账号管理</a></dd>
		<?php endif?>
		<dd><a href="<?php echo TCClick::app()->root_url, 'users/changePassword'?>">修改密码</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url, 'externalCodes/index'?>">外部访问码</a></dd>
		<dd><a href="<?php echo TCClick::app()->root_url, 'externalSites/index'?>">外部站管理</a></dd>
	</dl>
	<?php endif;?>
</div>