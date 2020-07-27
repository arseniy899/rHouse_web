<?
require('include.php');
$path = IO::getString('path');
$name = IO::getString('name');

$name = str_replace("..","",$name);
$name = str_replace("root","",$name);
$name = str_replace("//","",$name);
$name = str_replace("\\\\","",$name);

$path = Misc::denormalizeUserHubPath($path);
if (!file_exists($path))
	mkdir($path, 0777, true);
if(strstr($name,"."))
{
	$file = $path."/".$name;
	$handle = fopen($file, 'w') or die('Cannot open file:  '.$my_file); //implicitly creates file
}
echo IO::genErr(0);
