<?
require('include.php');

$obj = new Unit('', '');
$obj->id = IO::getString('id');
$obj->getInfoFRDB();
$obj->name = IO::getString('name');
$obj->color  = IO::getString('color');
$obj->icon = IO::getString('iconCust');
$obj->sectId = IO::getString('sectId');
$obj->uiShow = IO::getString('uiShow');
$obj->setInfoToDB();

echo DefErrors::showJSres($obj);