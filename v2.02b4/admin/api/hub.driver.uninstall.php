<?php
require ("include.php");
$cmd = $CONFIG['HUB']['mainPath']."/drivers/uninstall.sh ".IO::getString("name");
$data = Hub::getPayload(array(
	"cmd"		=> "system.cmd",
	"shell"		=> $cmd
));
$data = str_replace(array("\n"),array("<br />"),$data);
echo IO::showJSres($data);
?>