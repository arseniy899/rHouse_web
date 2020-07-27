<?php
require('include.php');
$table = IO::getString('m');
if(!isset($table))
	$table = 'units_run';

$id = IO::getString('id');
$createNew = True;
if( isset($id) && $id != "")
{
	$sql = " DELETE FROM `{$table}` WHERE `id`={$id}";
	$result = mysqli_query($db, $sql);
	if(mysqli_error($db) != null)
		echo IO::genErrMsg(3, "MySQL:".mysqli_error($db));
	if($result)
		exit(DefErrors::genErr(0));
	else
		exit(DefErrors::genErr(3));
}
exit(DefErrors::genErr(3));