<?
require('include.php');
$obj = new Unit('', '');
$obj->id = IO::getString('id');
$obj->getInfoFRDB();
$data = [	"values"=>$obj->getUnitVals(IO::getString('from'),IO::getString('to')),
			"unit" =>	$obj];
echo DefErrors::showJSres($data);