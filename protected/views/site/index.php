<h1>tcclick</h1>
<div class="block" style="padding:10px 20px">
	<?php if(defined('SAE_TMP_PATH')) include 'sae_requirement_check.php' ?>
	<p>tcclick是一个山寨 <a href="http://www.umeng.com/" target="_blank">友盟</a>
	的移动平台的开源用户统计项目，由于我们试用友盟一段时间感受到诸多问题，
	所以决定自己编写一套类友盟统计系统并将在项目较为稳定时开放源代码至github平台。</p>
	<p>至于友盟所感受到的问题，曾经向友盟的朋友所反馈过的三个疑问：
	<pre>1. 数据跟自己统计的数据存在偏差，真实性存疑
2. 试用的几天频繁故障，稳定性存疑
3. 数据托管在友盟，安全性存疑</pre></p>
	<p>本项目定位以及目标：<pre>1. 完全兼容SAE，绝大多数开发者通过每月免费赠送的1万云豆可以免费使用
2. 在SAE平台部署可以支持1000万以内的总用户数量级的应用
3. 自架服务器(两台,也许单台普通PC服务器)可支持亿级别的总用户数量级的应用(未来的目标)
	</pre></p>
	<p>tcclick相比于友盟，不同的地方有：
		<pre>1. tcclick开源，可自由部署，不限于SAE，数据完全自己掌控，无需担心泄露 
2. tcclick开源，算法逻辑完全公开，友盟闭源，算法逻辑未知
3. tcclick记录了小时活跃设备数据，目前友盟没有这类图表
4. ios版本的错误收集系统可以自动对收集到的错误进行符号表映射，即把 
     <span style="color:blue">0x0009e1c1 KankanIpad + 643521</span> 变成 
     <span style="color:blue">0x0005d311 -[ASIHTTPRequest startRequest] (in KankanIpad) (ASIHTTPRequest.m:1385)</span>
5. tcclick开源，可以非常方便的自行添加特定需求的报表</pre>
	</p>
	<p>
	服务器端源码：<a href="https://github.com/starfalling/tcclick-server" target="_blank">https://github.com/starfalling/tcclick-server</a>
	<br/>IOS端源码：<a href="https://github.com/starfalling/tcclick-ios" target="_blank">https://github.com/starfalling/tcclick-ios</a>
	<br/>安卓端源码：<a href="https://github.com/starfalling/tcclick-android" target="_blank">https://github.com/starfalling/tcclick-android</a>
	<br/>winPhone源码：还没写
	</p>
	<p>项目目前还有较多的功能细节缺失，文档编写工作也还没有开始启动，不过我们已经在
	<a href="http://app.91.com/Soft/iPhone/com.truecolor.kankan.ipad.KankanIpad-0.9.1-0.9.1.html" target="_blank">《千寻影视HD》</a>上投入了正式使用。
	目前项目还不成熟，不建议大家投入正式使用，请等待1.0版本。
	有兴趣或者疑问的朋友可以通过 gyq5319920@gmail.com 或者 
	<a href="http://weibo.com/u/1361956247" target="_blank">@YorkGu</a>
	联系我</p>
</div>