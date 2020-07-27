<?
require('include.php');

$id = IO::getString('id');

echo DefErrors::showJSres(IO::executeSqlParse("UPDATE `alerts` SET `active` = '0' WHERE `alerts`.`id` = {$id};") );