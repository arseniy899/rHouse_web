<div class="logout_icon" >
	<?if(IS_USER_ADMIN){?><a href="//<?=REMOTE_ROOT?>/admin/"><i class="icofont-fix-tools"></i></a><?}?>
	<?if($CONFIG['userPolicy']){?><i class="icofont-logout" onclick="logout()"></i><?}?>
</div>
