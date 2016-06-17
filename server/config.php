<?php
//MYSQL ROTZE
define('MYSQL_HOST', 'localhost');
define('MYSQL_USER', 'root');
define('MYSQL_PASSWORD', 'kaoscrew');
define('MYSQL_DATABASE', 'tentob');

//TABELLEN ROTZE
define('BOTS_TABLE', 'zombies');

date_default_timezone_set('Europe/Berlin');

error_reporting(E_ALL);
ini_set("display_errors", 1);

header('Content-type: text/html; charset=utf-8');
?>