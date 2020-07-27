<?php

	session_start();
	
	require_once('include/shared.inc.php');    
    require_once('include/settings.inc.php');    
	require_once('include/functions.inc.php');
	require_once('include/languages.inc.php');	

	function endsWith($string, $endString) 
	{ 
		$len = strlen($endString); 
		if ($len == 0) { 
			return true; 
		} 
		return (substr($string, -$len) === $endString); 
	} 
	
	$task = isset($_POST['task']) ? prepare_input($_POST['task']) : '';
	$passed_step = isset($_SESSION['passed_step']) ? (int)$_SESSION['passed_step'] : 0;
	
	// handle previous steps
	// -------------------------------------------------
	if($passed_step >= 5){
		// OK
	}else{
		header('location: start.php');
		exit;				
	}
	$skip = false;
	$next = true;
	if($task == 'send')
	{
		if($passed_step >= 5)
		{
			$mainPath = $_SESSION['config']['HUB']['mainPath'];
			$mainPath = str_replace("\\","/",$mainPath);
			$dbIP = $_SESSION['config']['DB']['host'];
			$dbDB = $_SESSION['config']['DB']['dbname'];
			$dbUser = $_SESSION['config']['DB']['login'];
			$dbPwd = $_SESSION['config']['DB']['password'];
			$hubSecret = $_SESSION['hub_secret'];
			$output = array();
			$command = ("cd \"{$mainPath}\" && python3 register.py secret={$hubSecret} webVer=".EI_APPLICATION_VERSION." dbIP={$dbIP} dbUser={$dbUser} dbDB={$dbDB} dbPwd={$dbPwd} 2>&1");
			exec($command, $output);
			$output = implode("\n",$output);
			if(strlen($output) <= 3)
			{
				$error_msg = "<b>Установка не может быть завершена:</b> ошибка проведения регистрации <br /><br />Для решения проблемы обратитесь в службу поддержки производителя.<br /> Справочная информация: ".$output;
				
				$next = false;
			}
			else if(strstr($output,"Error") || strstr($output,"msg"))
			{
				$error_msg = $output;
				if( strstr($output,"Hub server already registed"))
				{
					$error_msg = "<b>Установка не может быть завершена:</b> устройство уже было зарегистрировано в системе. <br /><br />Для решения проблемы обратитесь в службу поддержки производителя.";
					$next = false;
				}
					//$skip = true;
			}
			
			if(empty($error_msg) || $skip)
			{
				// CONFIGURING DataBase
				
				$db = new PDO("mysql:host={$_SESSION['config']['DB']['host']}", $_SESSION['config']['DB']['login'], $_SESSION['config']['DB']['password']);

				$query = file_get_contents("DB.sql");

				$stmt = $db->prepare($query);

				if (!$stmt->execute())
					$error_msg = "Проблема настройки базы данных";
				
			}
			if(empty($error_msg) || $skip)
			{
				$_SESSION['config']['timezone'] = 'Europe/Moscow';
				$_SESSION['config']['devicesIconsPath'] = 'img/metro-icons';
				$_SESSION['config']['domain'] = '';
				$_SESSION['config']['userPolicy'] = true;
				$_SESSION['config']['version'] = EI_APPLICATION_VERSION;
				
				
				//$output = preg_replace("/\{(?:[^{}]|(?R))*\}/", '', $output);
				$output = json_decode($output)->data;
				if(is_array ($output) )
					$output = $output[0];
				$_SESSION['config']['HUB']['ID'] = $output->id;
				$_SESSION['config']['HUB']['token'] = $output->token;
				
				$completed = false;
				$error_mg  = array();
					
				
				$fp = fopen($mainPath.'APItoken.txt', 'w');
				fwrite($fp, $output->token);
				fclose($fp);
				$CONFIG = $_SESSION['config'];
				$confStr = var_export($CONFIG, TRUE);
				$confStr = "<?
\$CONFIG = {$confStr};
					";
				file_put_contents('../config.php', $confStr);
				$completed = true;
					
				
				$_SESSION['passed_step'] = 6;
				header('location: complete_installation.php');
				exit;				
			}
		}else
			$error_mg[] = lang_key('alert_wrong_parameter_passed');
	}
	else{
		
	} 
	
	
?>	

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo lang_key("installation_guide"); ?> | <?php echo lang_key('ready_to_install'); ?></title>

	
	<link rel="stylesheet" type="text/css" href="templates/<?php echo EI_TEMPLATE; ?>/css/styles.css" />
	<?php
		if($curr_lang_direction == 'rtl'){
			echo '<link rel="stylesheet" type="text/css" href="templates/'.EI_TEMPLATE.'/css/rtl.css" />'."\n";
		}
	?>

	<script type="text/javascript" src="js/main.js"></script>
	<script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>
	<?php
		if(file_exists('languages/js/'.$curr_lang.'.js')){
			echo '<script type="text/javascript" src="language/'.$curr_lang.'/js/common.js"></script>';
		}else{
			echo '<script type="text/javascript" src="language/en/js/common.js"></script>';
		}
	?>
</head>
<body>
<?draw_side_navigation(6);?>
<div class="top-bar"><?php echo lang_key('new_installation_of'); ?> <?php echo EI_APPLICATION_NAME.' '.EI_APPLICATION_VERSION;?></div>
<div id="main">
	<h2 class="sub-title"><?php echo lang_key('sub_title_message'); ?></h2>
	
	<div id="content">
		<div class="central-part">
			<h2><?php echo lang_key('step_7_of'); ?> - <?php echo lang_key('ready_to_install'); ?></h2>
			<h3><?php echo lang_key('we_are_ready_to_installation'); ?></h3>			
		
			<p><?php echo lang_key('we_are_ready_to_installation_text'); ?></p>			
		
			<form method="post" action="ready_to_install.php">
			<?php
				if(!empty($error_msg)){
					echo '<div class="alert alert-error">'.$error_msg.'</div>';
				}
				/*if(!empty($error_msg)){
					?>
					<div class="alert alert-warning">.</div>
					<a href="administrator_account.php" class="form_button" /><?php echo lang_key('back'); ?></a><?
				}*/
			?>
			<input type="hidden" name="task" value="send" />
			<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
			
			<table width="100%" border="0" cellspacing="1" cellpadding="1">
			<tr><td nowrap height="10px" colspan="3"></td></tr>

			<tr><td colspan="2" nowrap height="20px">&nbsp;</td></tr>
			<tr>
				<td colspan="2">
					<a href="hub_secret.php" class="form_button" /><?php echo lang_key('back'); ?></a>
					&nbsp;&nbsp;&nbsp;&nbsp;
					<?if($next)
					{?>
					<input type="submit" class="form_button" value="<?php echo lang_key('continue'); ?>" />
					<?}?>
				</td>
			</tr>                        
			</table>
			</form>                        

		</div>
		<div class="clear"></div>
	</div>
	
	<?php include_once('include/footer.inc.php'); ?>        

</div>
</body>
</html>