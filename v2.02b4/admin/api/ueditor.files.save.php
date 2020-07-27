<?
require('include.php');
$content = file_get_contents('php://input');
$path = strtok($content, "\n");
$path = Misc::denormalizeUserHubPath($path);
$file = substr( $content, strpos($content, "\n")+1 );
// Open the file to get existing content
file_put_contents($path, $file);
echo IO::genErr(0);