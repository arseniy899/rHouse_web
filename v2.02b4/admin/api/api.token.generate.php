<?php
require ("include.php");
$token = bin2hex(openssl_random_pseudo_bytes(32));
$CONFIG['HUB']['token'] = $token;
Misc::writeConfigArrToFile();
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="APItoken.txt"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . strlen($token));
echo $token;
exit;
?>