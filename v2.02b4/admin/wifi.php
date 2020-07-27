

<? require_once('include.php'); ?>
<?
	if(isset($_POST['sent']))
	{
		IO::showJSres(Hub::sendPayload(array(
			"cmd"		=> "wifi.update",
			"ssid"		=> $_POST['ssid'],
			"password"	=> $_POST['password'],
		)));
		exit();
	}
?>
<html>
	<head>
	<title>SmartHouse</title>
		<? require('templates/head.php'); ?>
	</head>
	
	<body>
		<div class="top-bar">WiFi settings</div>
		<? require('menu.php'); ?>
		
		<div id="page" style="">
			<form id="form" class="form no-reload" method="POST" action="">
				<div class='form-item-wrapper'><label for='intf'>SSID</label><input class="" type='text' name='ssid' value=''></div>
				<div class='form-item-wrapper'><label for='intf'>Password</label><input class="" type='password' name='password' value=''></div>
				<input type='hidden' name='sent' value='1'>
				<input type="submit" value="Сохранить">
				</div>
			</form>
		</div>
		<? require('templates/bottom.php'); ?>
	</body>
</html>

