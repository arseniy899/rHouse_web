<?php
require('include.php');
$table = IO::getString('m');
$data = array();

if(isset($table) && $table != "")
{
	$order = IO::getString('order');
	if( empty($order) )
		$sql = "SELECT * FROM `{$table}` LIMIT 1000";
	else
		$sql = "SELECT * FROM `{$table}` ORDER BY {$order} LIMIT 1000";
	$data['rows']=IO::executeSqlParse($sql);
}
if(IO::getString('sections') != '')
{
	$sql ="SELECT * FROM `units_sections`";
	
	$res = mysqli_query($db,$sql);
	if(mysqli_error($db) != null)
		echo IO::genErrMsg(3, "MySQL:".mysqli_error($db));
	else if (IO::isQueryOK($res))
	{
		$rows = array();
		while($r = mysqli_fetch_assoc($res))
			$rows[$r['id']] = $r;
		$data['sections'] = $rows;
	}
	else
		$data['sections'] = false;
}
if(IO::getString('icons') != '')
{
	$data['icons'] = Misc::getDirFiles($CONFIG['devicesIconsPath']);
	$data['iconsPath'] = REMOTE_ROOT_PATH.$CONFIG['devicesIconsPath'];
}
if(IO::getString('uscripts') != '')
	$data['uscripts'] = Misc::getDirFiles($CONFIG['HUB']['uScriptsPath'], true);
if(IO::getString('unitsDef') != '')
	$data['unitsDef'] = Unit::getUnitsDefs();
if(IO::getString('units') != '')
	$data['units'] = Unit::getAllUnitsSql();
if(IO::getString('conditions') != '')
{
	$data['conditions']['=='] = "value == a";
	$data['conditions']['>='] = "value >= a";
	$data['conditions']['<='] = "value <= a";
	$data['conditions']['!='] = "value != a";
	$data['conditions']['any'] = "any value";
	$data['conditions']['contains'] = "value contains a";
}
if(IO::getString('scheduleTypes') != '')
{
	$data['scheduleTypes']['1'] = "ONCE";
	$data['scheduleTypes']['2'] = "REPEAT";
	
}
if(IO::getString('directions') != '')
{
	$data['directions']['0'] = "ANY";
	$data['directions']['1'] = "INPUT";
	$data['directions']['2'] = "SET";
}
echo IO::showJSres($data);