

<? require_once('include.php'); ?>
<?
	if(isset($_POST['sent']))
	{
		$CONFIG['HUB']['IP'] = $_POST['IP'];
		$CONFIG['HUB']['PORT'] = $_POST['PORT'];
		$CONFIG['HUB']['uScriptsPath'] = $_POST['uScriptsPath'];
		Misc::writeConfigArrToFile();
		
		exit();
	}
?>
<html>
	<head>
	<title>SmartHouse</title>
		<? require('templates/head.php'); ?>
	</head>
	
	<body>
		<div class="top-bar">Hub settings</div>
		<? require('menu.php'); ?>
		
		<div id="page" style="">
			<form id="form" class="form no-reload" method="POST" action="">
				<div class='form-item-wrapper'><label for='intf'>IP</label><input class="" type='text' name='IP' value='<?=$CONFIG['HUB']['IP']?>'></div>
				<div class='form-item-wrapper'><label for='intf'>Port</label><input class="" type='text' name='PORT' value='<?=$CONFIG['HUB']['PORT']?>'></div>
				<div class='form-item-wrapper'><label for='intf'>uScriptsPath</label><input class="" type='text' name='uScriptsPath' value='<?=$CONFIG['HUB']['uScriptsPath']?>'></div>
				<input type='hidden' name='sent' value='1'>
				<input type="submit" value="Сохранить">
			</form>
			<div class="button red"  onclick="if (window.confirm('Do you really want to reset old API-token and get new one?')) window.open('api/api.token.generate.php', 'Youre new token!');"> Reedem new token</div>
		</div>
		<? require('templates/bottom.php'); ?>
	</body>
</html>

