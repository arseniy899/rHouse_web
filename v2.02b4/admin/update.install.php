<? require_once('include.php'); ?>
<?
	$error = "";
	if(IO::getString("doUpdate"))
	{
		$getWebVer = IO::getString("doUpdate");
		$fileName = "v{$getWebVer}.zip";
		$file = fopen($fileName, "w+");
		$f = file_put_contents($fileName, fopen(Misc::$updateUrl.'web/'.$fileName, 'r'), LOCK_EX);
		if(FALSE === $f)
			$error = "Неудаётся скачать обновление ".$getWebVer;
		else
		{
			$zip = new ZipArchive;
			$res = $zip->open($fileName);
			if ($res === TRUE) {
				$zip->extractTo('../../');
				$zip->close();
				fclose($file);
				unlink($fileName);
				//Misc::chmod_R(dirname(__FILE__)."/../../", 0777);
				$CONFIG['version'] = "v{$getWebVer}";
				Misc::writeConfigArrToFile();
			} 
			else 
				$error =  "Ошибка установки обновления";
		}		
		if(!$error)
		{
			$_SESSION['updateAvailable'] = false;
			
			header("Location: //".INC_ROOT."v".$getWebVer."/?notes=show");
			echo IO::genErr(0);
			exit();
		}
	}
	else if(IO::getString("checkUpdate"))
	{
		$send = 'true';
		$getWebVer = Misc::checkUpdate();
		$getHubVer = Hub::getPayload(array(
			"cmd"		=> "uscript.run",
			"uscript"		=> "SYS/updateCheck.py"
		));
	}
	else
		$send = 'false';
?>
<html>
	<head>
	<style>
		.note
		{
			color: #616E14;
			border: solid 1px #BFD62F;
			background-color: #DAE691;
			-moz-border-radius: 6px;
			-webkit-border-radius: 6px;
			border-radius: 6px;
			padding: 14px 20px;
		}
		.error
		{
			color: #FFF;
			border: solid 1px #BFD62F;
			background-color: #e74c3c;
			-moz-border-radius: 6px;
			-webkit-border-radius: 6px;
			border-radius: 6px;
			padding: 14px 20px;
		}
	</style>
	<title>SmartHouse</title>
		<? require('templates/head.php'); ?>
	</head>
	<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
	
	<body>
		<div class="top-bar">Разделы</div>
		<? require('menu.php'); ?>
		
		<div id="page" style="">
			
			<?if($send == 'true' && $getWebVer ){?>
				<h1>Установка обновлений</h1>
				<div class="note">Доступно обновление интерфейса до версии <b><?=$getWebVer?></b>! Установить?</div>
				<?if($error){?>
				<div class="error">Ошибка выполения: <br /> <b><?=$error?></b>!</div>
				<?}?>
				<form id="form" class="" method="POST" action="">
					<input type='hidden' name='doUpdate' value='<?=$getWebVer?>'>
				<input type="submit" value="Установить!">
				
				<?
				if( strstr($getHubVer,'Cur version is OK'))
				{
					?><div class="note">Установлена последняя версия сервера</div><?
				}
				else
				{
					?><div class="note">Сервер был обновлен до последней доступной версии</div><?
				}
				?>
			</form>
				
			<?}else if($send == 'true'){?>
			<h1>Проверка обновлений</h1>
			<div class="note">Нет доступных для установки версий</div>
			<?}?>
			<h1>Проверка обновлений</h1>
				<p>Будет выполнена проверка обновлений и интерфейса, и сервера. При наличии, обновления для сервера будут установлены автоматически.</p>
				<form id="form2" class="" method="POST" action="">
					<input type='hidden' name='checkUpdate' value='1'>
				<input type="submit" value="Проверить">
			
		</div>
		<? require('templates/bottom.php'); ?>
	</body>
</html>

