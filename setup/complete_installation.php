<?php

	session_start();
	
	require_once('include/shared.inc.php');    
    require_once('include/settings.inc.php');
	require_once('include/database.class.php'); 
    require_once('include/functions.inc.php');	
	require_once('include/languages.inc.php');	
	
	$passed_step = isset($_SESSION['passed_step']) ? (int)$_SESSION['passed_step'] : 0;

	// handle previous steps
	// -------------------------------------------------
	if($passed_step >= 5){
		// OK
	}else{
		header('location: start.php');
		exit;				
	}
	
	if(EI_MODE == 'debug') error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
    
	$error_mg  = array();
		
	if($passed_step == 6)
	{
		$completed = true;
		
	}else{
		$error_mg[] = lang_key('alert_wrong_parameter_passed');
	}
        
?>	

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo lang_key('installation_guide'); ?> | <?php echo lang_key('complete_installation'); ?></title>

	
	<link rel="stylesheet" type="text/css" href="templates/<?php echo EI_TEMPLATE; ?>/css/styles.css" />
	<?php
		if($curr_lang_direction == 'rtl'){
			echo '<link rel="stylesheet" type="text/css" href="templates/'.EI_TEMPLATE.'/css/rtl.css" />'."\n";
		}
	?>

	<script type="text/javascript" src="js/main.js"></script>
	<script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>
</head>
<body>
<? draw_side_navigation(7);	?>
<div class="top-bar"><?php echo lang_key('new_installation_of'); ?> <?php echo EI_APPLICATION_NAME.' '.EI_APPLICATION_VERSION;?></div>
<div id="main">    
	<h2 class="sub-title"><?php echo lang_key('sub_title_message'); ?></h2>
	
	<div id="content">

		<div class="central-part">
			<h2><?php echo lang_key('step_7_of'); ?>
			<?php if(!$completed){ ?>
				- <?php echo lang_key('database_import_error'); ?>
			<?php }else{ ?>
				- <?php echo lang_key('completed'); ?>
				<!--<h3><?php //echo lang_key('updating_completed'); ?></h3>			-->
			<?php } ?>
			</h2>

			<?php
				if(!$completed){
					echo '<div class="alert alert-error">';
					foreach($error_mg as $msg){
						echo $msg.'<br>';
					}
					echo '</div>';
				}if(!$completed){
					echo '<div class="alert alert-error">';
					foreach($error_mg as $msg){
						echo $msg.'<br>';
					}
					echo '</div>';
				}
			?>
		
			<table width="99%" cellspacing="0" cellpadding="0" border="0">
			<tbody>
			<?php if(!$completed){ ?>
				<tr><td nowrap height="25px">&nbsp;</td></tr>
				<tr>
					<td>	
						<a href="ready_to_install.php" class="form_button"><?php echo lang_key('back'); ?></a>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="submit" class="form_button" onclick="javascript:location.reload();" value="<?php echo lang_key('complete'); ?>" />
					</td>
				</tr>							
			<?php }else{ ?>
				<tr><td>&nbsp;</td></tr>						
										
					<tr><td><h4><?php echo lang_key('installation_completed'); ?></h4></td></tr>
					<tr>
						<td>
							<div class="alert alert-success"><?php echo str_replace('_CONFIG_FILE_', EI_CONFIG_FILE_PATH, lang_key('file_successfully_created')); ?></div>
							<div class="alert alert-success">ID устройства: <b><?=$_SESSION['config']['HUB']['ID'] ?></b></div>
							<div class="alert alert-warning"><?php echo lang_key('alert_remove_files'); ?></div>
							<?php echo (EI_POST_INSTALLATION_TEXT != '') ? '<div class="alert alert-info">'.EI_POST_INSTALLATION_TEXT.'</div>' : ''; ?>
							<br /><br />
							<?php if(EI_APPLICATION_START_FILE != ''){ ?><a class="form_button" href="<?php echo '../'.EI_APPLICATION_START_FILE;?>">
							<?php echo lang_key('proceed_to_login_page'); ?></a><?php } ?>
						</td>
					</tr>															
				
			<?php } ?>
			</tbody>
			</table>
			<br>

			<?php
				if(EI_ALLOW_START_ALL_OVER && $completed){
					echo '<h3>'.lang_key('start_all_over').'</h3>';
					echo '<p>'.lang_key('start_all_over_text').'</p>';
					echo '<form action="start.php" method="post">';
					echo '<input type="hidden" name="task" value="start_over" />';
					echo '<input type="hidden" name="token" value="'.$_SESSION['token'].'" />';
					echo '<input type="submit" class="form_button" name="btnSubmit" value="'.lang_key('remove_configuration_button').'" />';
				}
			?>			
			
		</div>
		<div class="clear"></div>
	</div>
	
	<?php include_once('include/footer.inc.php'); ?>        

</div>
</body>
</html>