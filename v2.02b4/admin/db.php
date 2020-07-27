

<? require_once('include.php'); ?>
<?
	if(isset($_POST['sent']))
	{
		$CONFIG['DB']['host'] = $_POST['host'];
		$CONFIG['DB']['login'] = $_POST['login'];
		$CONFIG['DB']['password'] = $_POST['password'];
		$CONFIG['DB']['dbname'] = $_POST['dbname'];
		$CONFIG['DB']['charset'] = $_POST['charset'];
		Misc::writeConfigArrToFile();
		IO::showJSres(Hub::sendPayload(array(
			"cmd"		=> "config.update.db",
			"host"		=> $CONFIG['DB']['host'],
			"login"		=> $CONFIG['DB']['login'],
			"password"	=> $CONFIG['DB']['password'],
			"dbname"	=> $CONFIG['DB']['dbname'],
			"charset"	=> $CONFIG['DB']['charset'],
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
		<div class="top-bar">Database settings</div>
		<? require('menu.php'); ?>
		
		<div id="page" style="">
			<form id="form" class="form no-reload" method="POST" action="">
				<div class='form-item-wrapper'><label for='intf'>Host</label><input class="" type='text' name='host' value='<?=$CONFIG['DB']['host']?>'></div>
				<div class='form-item-wrapper'><label for='intf'>Login</label><input class="" type='text' name='login' value='<?=$CONFIG['DB']['login']?>'></div>
				<div class='form-item-wrapper'><label for='intf'>Password</label><input class="" type='password' name='password' value='<?=$CONFIG['DB']['password']?>'></div>
				<div class='form-item-wrapper'><label for='intf'>Base name</label><input class="" type='text' name='dbname' value='<?=$CONFIG['DB']['dbname']?>'></div>
				<div class='form-item-wrapper'><label for='intf'>Char set</label><input class="" type='text' name='charset' value='<?=$CONFIG['DB']['charset']?>'></div>
				<input type='hidden' name='sent' value='1'>
				<input type="submit" value="Сохранить">
				</div>
			</form>
		</div>
		<? require('templates/bottom.php'); ?>
	</body>
</html>

