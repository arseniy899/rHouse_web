<?
require('include.php');

$id 			 = IO::getString('id');
$name 			 = IO::getString('name');
$isDefHidden 	 = IO::getString('isDefHidden');

$sql = "UPDATE `units_sections` SET `name` = '{$name}', `isDefHidden` = '{$isDefHidden}' WHERE `units_sections`.`id` = {$id}";
$result = mysqli_query($db, $sql);
if(mysqli_error($db) != null)
	echo IO::genErrMsg(3, "MySQL:".mysqli_error($db));
if($result)
	exit(DefErrors::genErr(0));
else
	exit(DefErrors::genErr(3));
