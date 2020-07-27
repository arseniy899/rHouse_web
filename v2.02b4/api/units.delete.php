<?
require('include.php');

$obj = new Unit('', '');
$obj->id = IO::getString('id');


echo DefErrors::showJSres($obj->deleteDB());