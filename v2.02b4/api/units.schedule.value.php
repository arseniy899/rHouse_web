<?
require('include.php');
//$unid = IO::getString('unid');
//$setid = IO::getString('setid');
//$id = IO::getString('id');
$obj = new Unit('', '');
$obj->id = IO::getString('id');
$obj->getInfoFRDB();
$value = IO::getString('value');

$date = IO::getString('date');
//echo $value;

echo DefErrors::showJSres($obj->scheduleValueSet( $value, $date));