<?
if(!file_exists (rtrim($_SERVER["CONTEXT_DOCUMENT_ROOT"],"/")."/config.php")   )
{
	if(!strstr(REQUESTED_PATH,"setup"))
	{
		header("Location: //".$_SERVER["SERVER_NAME"]);
		exit();
	}
	
}

require (__DIR__."/../config.php");
require ("core/const.php");
require ("core/loader.php");
require ("core/access.check.php");
require ("core/mysql.php");
