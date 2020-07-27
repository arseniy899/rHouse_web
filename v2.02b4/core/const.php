<?
error_reporting(E_ALL);
ini_set('display_errors', true);
session_name('Private'); 
session_start(); 

if(isset($_COOKIE["user"]))
{
	$_SESSION['user'] = unserialize(base64_decode($_COOKIE["user"]));
	if(isset($_COOKIE['admin']) && $_COOKIE['admin'] == true)
		$_SESSION['admin'] = true;
}

define('DOMAIN', $_SERVER["SERVER_NAME"]);
$curvEr = "2.02b4";
define('SERVICE_IP', "http://192.168.5.1");
//define('SERVICE_IP', "http://192.168.1.10/shv2_portal");
define('INC_ROOT', DOMAIN.rtrim($_SERVER["CONTEXT_PREFIX"],"/")."/");
define('REMOTE_ROOT', INC_ROOT."v{$curvEr}");
define('REMOTE_ROOT_PATH', "//".DOMAIN."/{$_SERVER["CONTEXT_PREFIX"]}/v{$curvEr}");
define('LOCAL_ROOT', rtrim($_SERVER["CONTEXT_DOCUMENT_ROOT"],"/")."/");
define('LOCAL_VER_ROOT', rtrim($_SERVER["CONTEXT_DOCUMENT_ROOT"],"/")."/".$CONFIG['version']."/");
define('REMOTE_PATH', $_SERVER['SCRIPT_NAME']);
define('REQUESTED_FILE', basename($_SERVER["REQUEST_URI"], ".php"));
define('REQUESTED_PATH', $_SERVER["REQUEST_URI"]);
define('IS_USER_AUTHED', isset($_SESSION['user']));
define('IS_USER_ADMIN', (!isset($CONFIG['userPolicy']) || $CONFIG['userPolicy'] == false) ||
						(isset($_SESSION['admin']) && $_SESSION['admin']!=0));


if(IS_USER_AUTHED)
	$USER_OBJ = $_SESSION['user'];
