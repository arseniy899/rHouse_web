<?
require('include.php');
$oldPath = IO::getString('old');
$newPath = IO::getString('new');

$oldPath = Misc::denormalizeUserHubPath($oldPath);
$newPath = Misc::denormalizeUserHubPath($newPath);
if($oldPath == $newPath)
	exit (IO::genErr(0));


if (is_dir($oldPath)) {
	if ($dh = opendir($oldPath)) {
		while (($file = readdir($dh)) !== false) {
		echo '<br>Archivo: '.$file;
		//exclude unwanted 
		if ($file=="move.php")continue;
		if ($file==".") continue;
		if ($file=="..")continue;
		if ($file=="viejo2014")continue;
		if ($file=="viejo2013")continue;
		if ($file=="cgi-bin")continue;
		//if ($file=="index.php") continue; for example if you have index.php in the folder
		if (rename($oldPath.'/'.$file,$newPath.'/'.$file))
			{
			echo " Files Copyed Successfully";
			echo ": $newPath/$file"; 
			//if files you are moving are images you can print it from 
			//new folder to be sure they are there 
			}
			else {echo "File Not Copy";}
		}
		closedir($dh);
	}
}




else if (file_exists($oldPath) && !file_exists($newPath))
{
	rename($oldPath, $newPath);
	echo IO::genErr(0);
}
else
	echo IO::genErr(4005);