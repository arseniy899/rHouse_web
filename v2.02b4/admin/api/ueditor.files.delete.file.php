<?
require('include.php');
$path = IO::getString('path');
$path = Misc::denormalizeUserHubPath($path);
if (file_exists($path))
{
	unlink($path);
	echo IO::genErr(0);
}
else
	echo IO::genErr(4005);