<?php
$this->pageTitle=Yii::app()->name . ' - Login';

$imager = new Imager(getcwd()."/library/images/lock.png");
$imager->width = "16px";
$imager->height = "16px";
$imager->attributes["title"] = "This password is passed through 256-bit encryption for authentication.";
?>
<style>
div.authen-container div.input-container input {
	font-family: Verdana, Geneva, sans-serif;
	font-size:13px;
	padding:5px;
	letter-spacing:1px;
	margin-left:10px;
	margin-bottom:5px;
	width: 180px;
}
div.authen-container .required {
	font-weight:bold;
	color:#f00;
}
div.authen-container label {
	font-weight:bold;
	text-align:right;
	float:left;
	width:200px;
	margin-top:4px;
	padding:3px;
	margin-bottom:5px;
	padding-top:6px;
	font-size:12px;
}
div.authen-container #submit {
	cursor:pointer;
}
div.authen-container #submit.disabled {
	background-color:#fff;
	color:#ccc;
	cursor:default;
}
div.authen-container {
	margin:auto;
	width:480px;
	border:1px solid #ccc;
	padding:8px;
}
div.authen-title {
	padding:5px;
	margin-bottom:12px;
}
div.input-container.submit {
	margin-top:10px;
}
</style>
<h1>Login</h1>

<?php if(isset($error) and $error != ""): ?>
<div class="ui-state-error" style="padding:6px;font-size:13px;margin-bottom:10px;"><?=$error;?></div>
<?php endif; ?>

<div class="ui-widget-content" style="padding:6px;font-size:13px;margin-bottom:10px;">Please fill out the following form with your identikey username and password:</div>

<div class="authen-container ui-widget-content">
	<div class="authen-title ui-widget-header">Authentication Needed</div>
	<form method="post" id="login-form">
		<div class="input-container">
			<label>Identikey Username <span class="required">*</span></label>
			<input type="text" name="username" />
		</div>
		<div class="input-container">
			<label>Identikey Password <span class="required">*</span></label>
			<input type="password" name="password" /> <?=$imager->render();?>
		</div>
		<div class="input-container calign submit">
				<input type="submit" id="submit" value="Login" />
		</div>
		<br class="clear" />
	</form>
</div>

<script>
jQuery(document).ready(function(){
	$("#submit").button();
	$("#submit").click(function(){
		$(this).addClass("ui-state-hover");
		$(this).addClass("disabled");
		$(this).prop("value","Logging in...");
		$("#login-form").submit();
		return true;
	});
});
</script>