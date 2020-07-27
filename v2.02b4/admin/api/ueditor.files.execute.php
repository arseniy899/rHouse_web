<?
require('include.php');

$data = Hub::getPayload(array(
			"cmd"		=> "uscript.run",
			"uscript"		=> str_replace("root/","",IO::getString('path'))
		));
$data = str_replace(array("\n"),array("<br />"),$data);
echo IO::showJSres($data);