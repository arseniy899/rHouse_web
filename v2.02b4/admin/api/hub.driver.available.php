<?php
require ("include.php");
$url = "http://".SERVICE_IP."/service/drivers/list.get.php?search=".IO::getString("search");
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_VERBOSE,true);
curl_setopt($ch, CURLOPT_URL,$url);
$result=curl_exec($ch);
curl_close($ch);
echo($result);
?>