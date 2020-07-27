<?php
################################################################################
##              -= YOU MAY NOT REMOVE OR CHANGE THIS NOTICE =-                 #
## --------------------------------------------------------------------------- #
##  ApPHP EasyInstaller Pro version                                            #
##  Developed by:  ApPHP <info@apphp.com>                                      #
##  License:       GNU LGPL v.3                                                #
##  Site:          https://www.apphp.com/php-easyinstaller/                    #
##  Copyright:     ApPHP EasyInstaller (c) 2009-2013. All rights reserved.     #
##                                                                             #
################################################################################

	session_start();
	
	require_once('include/shared.inc.php');    
    require_once('include/settings.inc.php');    
	require_once('include/functions.inc.php');
	require_once('include/languages.inc.php');	

	$task = isset($_POST['task']) ? prepare_input($_POST['task']) : '';
	$passed_step = isset($_SESSION['passed_step']) ? (int)$_SESSION['passed_step'] : 0;
	$focus_field = 'admin_username';
	$error_msg = '';
	
	// handle previous steps
	// -------------------------------------------------
	if($passed_step >= 3){
		// OK
	}else{
		header('location: start.php');
		exit;				
	}
	
	// handle form submission
	// -------------------------------------------------
	if($task == 'send'){

		
		$secret = isset($_POST['secret']) ? prepare_input($_POST['secret']) : '';
		
		// validation here
		// -------------------------------------------------
		if($secret == '')
		{
			$focus_field = 'secret';
			$error_msg = lang_key('alert_secret_empty');				
		}
		else
		{


			if(empty($error_msg)){
				$_SESSION['hub_secret'] = $secret;
				$_SESSION['passed_step'] = 4;
				header('location: paths.php');
				exit;
			}
		}
	}
	else
	{
		$secret	= isset($_SESSION['hub_secret']) ? $_SESSION['hub_secret']: '';
		
	}
?>	

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo lang_key("installation_guide"); ?> | <?php echo lang_key('hub_secret'); ?></title>

	
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
<?php draw_side_navigation(4);?>
<div class="top-bar"><?php echo lang_key('new_installation_of'); ?> <?php echo EI_APPLICATION_NAME.' '.EI_APPLICATION_VERSION;?></div>
<div id="main">
	<h2 class="sub-title"><?php echo lang_key('sub_title_message'); ?></h2>
	
	<div id="content">
		<div class="central-part">
			<h2><?php echo lang_key('step_4_of'); ?> - <?php echo lang_key('hub_secret'); ?></h2>
			<h3><?php echo lang_key('admin_access_data'); ?></h3>
			<p><?php echo lang_key('admin_access_data_descr'); ?></p>

			<form method="post">
			<input type="hidden" name="task" value="send" />
			<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
			
			<?php
				if(!empty($error_msg)){
					echo '<div class="alert alert-error">'.$error_msg.'</div>';
				}
			?>

			<table width="100%" border="0" cellspacing="1" cellpadding="1">
			<?php if(EI_USE_ADMIN_ACCOUNT){ ?>
			<tr>
				<td colspan="3"><span class="star">*</span> <?php echo lang_key('alert_required_fields'); ?></td>
			</tr>
			<tr><td nowrap height="10px" colspan="3"></td></tr>
			<tr>
				<td>&nbsp;<?php echo lang_key('hub_secret'); ?>&nbsp;<span class="star">*</span></td>
				<td><input name="secret" id="secret" class="form_text" type="password" size="28" maxlength="22" value="<?php echo $secret; ?>" onfocus="textboxOnFocus('notes_secret')" onblur="textboxOnBlur('notes_secret')" <?php if(EI_MODE != 'debug') echo 'autocomplete="off"'; ?> /></td>
			</tr>
			
			<?php }else{ ?>
				<tr><td colspan="2"><?php echo lang_key('administrator_account_skipping'); ?></td></tr>			
			<?php } ?>
			<tr><td colspan="2" nowrap height="50px">&nbsp;</td></tr>
			<tr>
				<td colspan="2">
					<a href="database_settings.php" class="form_button" /><?php echo lang_key('back'); ?></a>
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
