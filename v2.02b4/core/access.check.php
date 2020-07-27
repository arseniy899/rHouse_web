<?
if(!IS_USER_AUTHED && (!isset($CONFIG['userPolicy']) || $CONFIG['userPolicy'] == true) )
{
	
	if(!strstr(REQUESTED_PATH,"api") && !strstr(REQUESTED_FILE,"auth"))
	{
		header("Location: //".REMOTE_ROOT."/auth.php");
		exit();
	}
	else if(strstr(REQUESTED_PATH,"api") && !strstr(REQUESTED_PATH,"login"))
		exit(IO::genErr(1000));
}
else if(strstr(REQUESTED_PATH,"/auth.php"))
{
	header("Location: //". REMOTE_ROOT."/index.php");
	exit();
}
if( !IS_USER_ADMIN && strstr(realpath(NULL), "admin") )
{
	header("Location: //". REMOTE_ROOT);
	exit();
}

