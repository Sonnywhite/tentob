<?php
// FUNCTION

// sets up a connection to the configurated DB and selects DB
$link = mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASSWORD) or die("Keine Verbindung zur DB: ".mysql_error());
mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $link);
mysql_select_db(MYSQL_DATABASE) or die("Auswahl der Datenbank fehlgeschlagen");
?>