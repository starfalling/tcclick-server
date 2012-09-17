<h1>修改登录密码</h1>
<div class="block" style="padding:10px 20px">
	<form method="post">
		<div class="row">
			<label>旧密码：</label>
			<input name="old_password" size="30" type="password"/>
		</div>
		<div class="row">
			<label>新密码：</label>
			<input name="new_password" size="30" type="password"/>
		</div>
		<div class="row">
			<label>重复新密码：</label>
			<input name="new_password_repeat" size="30" type="password"/>
		</div>
		<div class="row submit">
			<input type="submit" value="登录"/>
		</div>
	</form>
</div>
<style>form .row label{width:100px;}</style>
<script>$(function(){
	$("form").submit(function(){
		var new_password = $("input[name=new_password]").get(0).value;
		var new_password_repeat = $("input[name=new_password_repeat]").get(0).value;
		if(new_password_repeat != new_password){
			alert("两次密码输入不匹配");
			return false;
		}
	});
});</script>