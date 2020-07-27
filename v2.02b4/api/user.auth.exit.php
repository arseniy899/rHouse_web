<?php

require ("include.php");

session_unset();
if (ini_get("session.use_cookies")) 
{ 
   $params = session_get_cookie_params(); 
   setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"] ); 
} // Finally, destroy the session. 
$_COOKIE = [];
setcookie('login', '', 0, "/",DOMAIN); // 86400 = 1 day
setcookie('admin', '', 0, "/",DOMAIN); // 86400 = 1 day
setcookie('ttd', '', 0, "/",DOMAIN); // 86400 = 1 day
$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
foreach ($cookies as $cookie) 
{

	$fields = explode("=", $cookie);
	$name = trim($fields[0]);

	// unset any cookie for the current path
	setcookie($name, "", time() - 3600);

	// unset the cookie for the root path
	setcookie($name, "", time() - 3600, "/");
}
session_unset();
session_destroy();
$_SESSION['user'] = '';
$_SESSION = [];
//header("Location: //". $domain);
//header("Location: //". REMOTE_ROOT);
echo(DefErrors::genErr(0));
//exit();
?>