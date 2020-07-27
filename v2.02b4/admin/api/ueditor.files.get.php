<?
require('include.php');
$path = IO::getString('path');
$path = Misc::denormalizeUserHubPath($path);
if (file_exists($path)) {
	readfile($path);
	exit;
}
else
{
	http_response_code(404);
	exit("Error 404");
}