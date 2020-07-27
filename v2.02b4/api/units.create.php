<?
require('include.php');
$unid = IO::getString('unid');
$intf = 0;//IO::getString('intf');
$color = IO::getString('color');
$uiShow = IO::getString('uiShow');
$icon = IO::getString('icon');
$sectId = IO::getString('sectId');

$obj = Unit::createNew($unid, $intf, $color, $uiShow, $sectId, $icon);
echo DefErrors::showJSres( $obj);