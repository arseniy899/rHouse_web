<?php
require ("include.php");
$files = scandir($CONFIG['HUB']['mainPath']."/drivers");
$data = [];
$data["rows"]=[];
foreach( $files as $file)
{
	if(strlen($file) >=3 && !strstr($file,".sh") && !strstr($file,".py") && !strstr($file,".ini") && $file[0] != "_" && $file != "import" )
		$data["rows"][] = ["name"=>$file,"ver"=>""];
}
echo IO::showJSres($data);
?>