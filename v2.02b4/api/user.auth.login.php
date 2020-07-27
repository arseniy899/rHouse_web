<?
require ("include.php");
$login = IO::getString('login');
$password = IO::getString('passw');
echo User::authLogin($login, $password);
