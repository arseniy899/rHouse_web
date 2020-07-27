<?
require('include.php');
$oldPath = IO::getString('old');
$newPath = IO::getString('new');

$oldPath = Misc::denormalizeUserHubPath($oldPath);
$newPath = Misc::denormalizeUserHubPath($newPath);
if($oldPath == $newPath)
	echo IO::genErr(0);
else if (file_exists($oldPath) && !file_exists($newPath))
{
	rename($oldPath, $newPath);
	echo IO::genErr(0);
}
else
	echo IO::genErr(4005);