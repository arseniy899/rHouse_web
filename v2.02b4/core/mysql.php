<?

$db = mysqli_connect($CONFIG['DB']['host'],$CONFIG['DB']['login'],$CONFIG['DB']['password'],$CONFIG['DB']['dbname']);
$db->set_charset($CONFIG['DB']['charset']);
