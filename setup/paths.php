<?php

	session_start();
	
	require_once('include/shared.inc.php');    
    require_once('include/settings.inc.php');    
	require_once('include/database.class.php'); 
	require_once('include/functions.inc.php');
	require_once('include/languages.inc.php');	
	
	$task = isset($_POST['task']) ? prepare_input($_POST['task']) : '';
	$passed_step = isset($_SESSION['passed_step']) ? (int)$_SESSION['passed_step'] : 0;
	$program_already_installed = false;
	$focus_field = 'IP';
	$error_msg = '';
	
	// handle previous steps
	// -------------------------------------------------
	if($passed_step >= 2){
		// OK
	}else{
		header('location: start.php');
		exit;				
	}

	// handle form submission
	// -------------------------------------------------
	if($task == 'send'){
		$mainPath		= isset($_POST['mainPath']) 		? $_POST['mainPath'] 	:  realpath ('../../hub/');
		$IP				= isset($_POST['IP']) 				? $_POST['IP'] 			: 'localhost';
		$PORT 			= isset($_POST['PORT']) 			? $_POST['PORT'] 		: '4044';
		$uScriptsPath	= isset($_POST['uScriptsPath']) 	? $_POST['uScriptsPath']: realpath ('../../hub/uscripts');
		
		
		// validation here
		// -------------------------------------------------
		if($mainPath == ''){
			$focus_field = 'mainPath';
			$error_msg = lang_key('alert_mainPath_empty');	
		}
		else if($mainPath == '')
		{
			$focus_field = 'mainPath';
			$error_msg = lang_key('alert_mainPath_empty');	
		}
		
		else if($IP == '')
		{
			$focus_field = 'IP';
			$error_msg = lang_key('alert_IP_empty');	
		}
		
		else if($PORT == '')
		{
			$focus_field = 'PORT';
			$error_msg = lang_key('alert_PORT_empty');	
		}
		
		
		else if($uScriptsPath == '')
		{
			$focus_field = 'uScriptsPath';
			$error_msg = lang_key('alert_uScriptsPath_empty');	
		}
		else
		{
			$mainPathWr = is_writable($mainPath);
			$uScriptsPathWr = is_writable($uScriptsPath);
			if(!$mainPathWr && empty($error_msg))
				$error_msg = 'Ошибка пути к серверу дома';
			if(!$uScriptsPathWr && empty($error_msg))
				$error_msg = 'Ошибка пути к пользовательским скриптам';
			if(empty($error_msg))
			{
				$_SESSION['config']['HUB']['mainPath'] = $mainPath;
				$_SESSION['config']['HUB']['IP'] = $IP;
				$_SESSION['config']['HUB']['PORT'] = $PORT;
				$_SESSION['config']['HUB']['uScriptsPath'] = $uScriptsPath;
				
				$_SESSION['passed_step'] = 5;
				header('location: ready_to_install.php');
				exit;				
			}
		}		
	}else{
		$mainPath		= isset($_SESSION['config']['HUB']['mainPath']) 	? $_SESSION['config']['HUB']['mainPath'] 	:  realpath ('../../hub/');
		$IP				= isset($_SESSION['config']['HUB']['IP']) 			? $_SESSION['config']['HUB']['IP'] 			: 'localhost';
		$PORT 			= isset($_SESSION['config']['HUB']['PORT']) 		? $_SESSION['config']['HUB']['PORT'] 		: '4044';
		$uScriptsPath	= isset($_SESSION['config']['HUB']['uScriptsPath']) ? $_SESSION['config']['HUB']['uScriptsPath']: realpath ('../../hub/uscripts');
		
	} 

	// handle previous installation
	// -------------------------------------------------
    if(file_exists(EI_CONFIG_FILE_PATH)){        
		$program_already_installed = true;
		if($install_type == 'create'){
			if(EI_ALLOW_UPDATE) $install_type = 'update';
			else if(EI_ALLOW_UN_INSTALLATION) $install_type = 'un-install';
		}
		include_once(EI_CONFIG_FILE_PATH);
		if(defined('HUB_PREFIX')) $database_prefix = HUB_PREFIX;	
		///header('location: ../'.EI_APPLICATION_START_FILE);
        ///exit;
	}	
	
?>	

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo lang_key("installation_guide"); ?> | <?php echo lang_key('hub_connection'); ?></title>

	
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
<body onload="bodyOnLoad()">
<?draw_side_navigation(5);?>
<div class="top-bar"><?php echo lang_key('new_installation_of'); ?> <?php echo EI_APPLICATION_NAME.' '.EI_APPLICATION_VERSION;?></div>
<div id="main">
	<h2 class="sub-title"><?php echo lang_key('sub_title_message'); ?></h2>
	
	<div id="content">
		<div class="central-part">
			<h2><?php echo lang_key('step_5_of'); ?> - <?php echo lang_key('hub_connection'); ?></h2>
			
			<form method="post">
			<input type="hidden" name="task" value="send" />
			<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />

			<?php
				if(!empty($error_msg)){
					echo '<div class="alert alert-error">'.$error_msg.'</div>';
				}
			?>

			<table width="99%" border="0" cellspacing="1" cellpadding="1">
			<tr>
				<td colspan="3"><span class="star">*</span> <?php echo lang_key('alert_required_fields'); ?></td>
				
			</tr>
			<tr><td nowrap height="10px" colspan="3"></td></tr>
			
			<tr>
				<td nowrap>&nbsp;<?php echo lang_key('mainPath'); ?>: <span class="star">*</span></td>
				<td>
					<input type="text" class="form_text" name="mainPath" id="mainPath" size="30" <?php if(EI_MODE != 'debug') echo 'autocomplete="off"'; ?> value="<?php echo $mainPath; ?>" />					
				</td>
				
			</tr>
			
			<tr>
				<td nowrap>&nbsp;<?php echo lang_key('IP'); ?>: <span class="star">*</span></td>
				<td>
					<input type="text" class="form_text" name="IP" id="IP" size="30" <?php if(EI_MODE != 'debug') echo 'autocomplete="off"'; ?> value="<?php echo $IP; ?>" />					
				</td>
				
			</tr>
			
			<tr>
				<td nowrap>&nbsp;<?php echo lang_key('PORT'); ?>: <span class="star">*</span></td>
				<td>
					<input type="text" class="form_text" name="PORT" id="PORT" size="30" <?php if(EI_MODE != 'debug') echo 'autocomplete="off"'; ?> value="<?php echo $PORT; ?>" />					
				</td>
				
			</tr>
			
			<tr>
				<td nowrap>&nbsp;<?php echo lang_key('uScripts_path'); ?>: <span class="star">*</span></td>
				<td>
					<input type="text" class="form_text" name="uScriptsPath" id="uScriptsPath" size="30" <?php if(EI_MODE != 'debug') echo 'autocomplete="off"'; ?> value="<?php echo $uScriptsPath; ?>" />					
				</td>
				
			</tr>
			
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="button" class="form_button" title="<?php echo lang_key('test_paths'); ?>" onclick="testPaths()" value="<?php echo lang_key('test_paths'); ?>" />
				</td>
			</tr>
			<tr><td nowrap height="10px" colspan="3"></td></tr>
			<tr>
				<td colspan="2">
					<a href="hub_secret.php" class="form_button" /><?php echo lang_key('back'); ?></a>
					&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="submit" class="form_button" value="<?php echo lang_key('continue'); ?>" />
				</td>
			</tr>                        
			</table>
			</form>                        

		</div>
		<div class="clear"></div>
	</div>
	
	<?php include_once('include/footer.inc.php'); ?>        

</div>

<script type="text/javascript">
	function bodyOnLoad(){
		setFocus("<?php echo $focus_field; ?>");
		
	}	
</script>
</body>
</html>
