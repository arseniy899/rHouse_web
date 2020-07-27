<?
//var_dump($_SERVER);
//function spl_autoload_register($class_name) { require_once __DIR__."/../classes/".$class_name . '.php';}
spl_autoload_register(function($class_name) {
    include __DIR__."/../classes/".$class_name . '.php';
});
set_error_handler('error_handler');
//if(empty($_POST) && isset($_GET))
	//$_POST = $_GET;
function error_handler($errno, $errstr, $errfile, $errline)
{
	if( ($errno & error_reporting()) > 0 )
		exit (IO::genErrMsg(-1, "$errstr ($errno)<br />$errfile, $errline\n"));
		//throw new ErrorException($errstr, 500, $errno, $errfile, $errline);
	else
		return false;
}
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set($CONFIG['timezone']);