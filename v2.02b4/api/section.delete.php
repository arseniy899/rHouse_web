<?
require('include.php');

$id = IO::getString('id');

$sql = "DELETE FROM `units_sections` WHERE `units_sections`.`id` = {$id}";
$result = mysqli_query($db, $sql);
if(mysqli_error($db) != null)
	echo IO::genErrMsg(3, "MySQL:".mysqli_error($db));
if($result)
	exit(DefErrors::genErr(0));
else
	exit(DefErrors::genErr(3));
