<?php
// FUNCTION

// sets up a connection to the configurated DB and selects DB
//$link = mysqli_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASSWORD) or die("Keine Verbindung zur DB: ".mysqli_error());
$link = mysqli_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASSWORD,MYSQL_DATABASE);
mysqli_query($link, "SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
//mysqli_select_db(MYSQL_DATABASE) or die("Auswahl der Datenbank fehlgeschlagen");
?>