<?
require('include.php');
$data = array();
$data['alerts'] = IO::executeSqlParse("SELECT * FROM `alerts` WHERE `active` = 1");
if(IS_USER_ADMIN)
{
	
	if( !isset($_SESSION['updateAvailable']))
	{
		$_SESSION['updateAvailable'] = Misc::checkUpdate();
	}
	if($_SESSION['updateAvailable'])
	{
		$data['alerts'][] =  [ 
				"id"	=> "1", 
				"unid"	=> "", 
				"setid"	=> "", 
				"code"	=> "", 
				"type"	=> "info",
				"text"	=> "<a href='//".REMOTE_ROOT."/admin/update.install.php'>Доступно обновление. Нажмите здесь...</a>", 
				"time"	=> ""];
	}
}
if(IO::getInt('units') != 0)
{
	$data['units'] = Unit::selectRecentUnits();
	if($data['units'] !== false)
		foreach ($data['units'] as $obj)
			$obj->icon = $CONFIG['devicesIconsPath'].'/'.$obj->icon;
}
echo DefErrors::showJSres($data);

