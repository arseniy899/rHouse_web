<?
require('include.php');

$name 			 = IO::getString('name');
$isDefHidden 	 = IO::getString('isDefHidden');

$sql = "INSERT INTO `units_sections` (`name`, `isDefHidden`) VALUES ('{$name}', '{$isDefHidden}')";
$result = mysqli_query($db, $sql);
if(mysqli_error($db) != null)
	echo IO::genErrMsg(3, "MySQL:".mysqli_error($db));
if($result)
	exit(DefErrors::genErr(0));
else
	exit(DefErrors::genErr(3));
