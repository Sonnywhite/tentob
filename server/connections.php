<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
include_once('config.php');
include_once('setup_db_conn.php');

// getting connections of last hour

$query = "SELECT DATE_FORMAT(visits.first_visit,'%H:%i') AS time, COUNT(visits.session_id) AS count FROM visits WHERE visits.first_visit BETWEEN DATE_SUB(NOW(),INTERVAL 61 MINUTE) AND NOW() GROUP BY time ORDER BY time ASC";

$date = new DateTime();
date_sub($date, date_interval_create_from_date_string('60 minutes'));

$i = 0;
while($i <61) {
	$query = "SELECT *, DATE_FORMAT(visits.first_visit,'%H:%i') AS time, COUNT(visits.session_id) AS count FROM visits GROUP BY time HAVING time = '".$date->format("H:i")."'";
	echo $date->format("H").','.$date->format("i").',0,';
	if($result = mysqli_query($link,$query)) {
		if($row = mysqli_fetch_assoc($result))
			echo $row["count"].",";
		else
			echo "0,";
		mysqli_free_result($result);
	} else {
		echo "0,";
	}
	$i++;
	if($i<61)
		echo ";";
	date_add($date, date_interval_create_from_date_string('1 minutes'));
}

mysqli_close($link);

?>