<?
require('include.php');
$data = Misc::getDirRecur($CONFIG['HUB']['uScriptsPath']);
$data = array( 'children'  => $data, 'text' => 'root' );
echo DefErrors::showJSres($data );