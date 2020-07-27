<?
if(!file_exists (__DIR__."/config.php")   )
{
	
	if(!strstr(REQUESTED_PATH,"setup"))
	{
		header("Location: setup?lang=ru");
		exit();
	}
	
}
require_once('config.php'); 
include($CONFIG['version'].'/index.php'); 

//header("Location: //".$_SERVER["SERVER_NAME"]."/".$CONFIG['domain']."/".$CONFIG['version']); /* Redirect browser */
//exit();