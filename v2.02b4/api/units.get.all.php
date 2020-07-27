<?
require('include.php');
$data = array();

$units = Unit::getAllUnitsSql();
if($units === FALSE)
{
	exit( DefErrors::showJSres( array() ) );
}
foreach ($units as $obj)
	$obj->icon = '//'.REMOTE_ROOT.'/'.$CONFIG['devicesIconsPath'].'/'.$obj->icon;
$data['units'] = $units;

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

echo IO::showJSres( $data);
