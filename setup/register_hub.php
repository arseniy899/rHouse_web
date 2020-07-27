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
	$skip = false;
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
		
		$mainPath = __DIR__."/".$_SESSION['config']['HUB']['mainPath'];
		$mainPath = str_replace("\\","/",$mainPath);
		$dbIP = $_SESSION['config']['DB']['host'];
		$dbDB = $_SESSION['config']['DB']['dbname'];
		$dbUser = $_SESSION['config']['DB']['login'];
		$dbPwd = $_SESSION['config']['DB']['password'];
		$hubSecret = $_SESSION['hub_secret'];
		$command = ("cd \"{$mainPath}\" && \"c:\Python27\python.exe\" register.py secret={$hubSecret} webVer=".EI_APPLICATION_VERSION." dbIP={$dbIP} dbUser={$dbUser} dbDB={$dbDB} dbPwd={$dbPwd} 2>&1");
		$output = shell_exec($command);
		if( strstr($output,"Error"))
		{
			$error_msg = $output;
			if( strstr($output,"Hub server already registed"))
				$skip = true;
		}
		if(empty($error_msg))
		{
			
			$output = json_decode($output);
			$_SESSION['config']['HUB']['ID'] = $output->id;
			$_SESSION['config']['HUB']['token'] = $output->token;
			$_SESSION['passed_step'] = 6;
			header('location: ready_to_install.php');
			exit;				
		}
		
	}else{
		
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
    <title><?php echo lang_key("installation_guide"); ?> | <?php echo lang_key('register_hub'); ?></title>

	
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
<div id="main">
	<div class="top-bar"><?php echo lang_key('new_installation_of'); ?> <?php echo EI_APPLICATION_NAME.' '.EI_APPLICATION_VERSION;?></div>
	<h2 class="sub-title"><?php echo lang_key('sub_title_message'); ?></h2>
	
	<div id="content">
		<?php
			draw_side_navigation(6);		
		?>
		<div class="central-part">
			<h2><?php echo lang_key('step_6_of'); ?> - <?php echo lang_key('register_hub'); ?></h2>
			<h3><?php echo lang_key('register_data'); ?></h3>
			<form method="post">
			<input type="hidden" name="task" value="send" />
			<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />

			<?php
				if(!empty($error_msg)){
					echo '<div class="alert alert-error">'.$error_msg.'</div>';
				}
			?>
			<?php
				if($skip)
				{
					?>
					<a href="ready_to_install.php" class="form_button" /><?php echo lang_key('skip'); ?></a>
					<?
				}
			?>
			<table width="99%" border="0" cellspacing="1" cellpadding="1">
			<tr>
				<td colspan="3"><span class="star">*</span> <?php echo lang_key('alert_required_fields'); ?></td>
				
			</tr>
			<tr><td nowrap height="10px" colspan="3"></td></tr>
			
			
			<tr><td nowrap height="10px" colspan="3"></td></tr>
			<tr>
				<td colspan="2">
					<a href="paths.php" class="form_button" /><?php echo lang_key('back'); ?></a>
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
