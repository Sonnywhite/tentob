<?php
include_once('config.php');

session_set_cookie_params(1,"/");
session_start();

include_once('setup_db_conn.php');

// new session?
$query = "SELECT * FROM visits WHERE session_id=".session_id();
$result = mysqli_query($link,$query);
if(!$result || mysqli_num_rows($result)==0) {
	
	// yet unknown session
	$query = "INSERT INTO visits (session_id) VALUES ('".session_id()."')";
	mysqli_query($link,$query);
} else {
	// known session
	mysqli_free_result($result);
}

mysqli_close($link);


?>
<html>
<head>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script type="text/javascript">
	google.charts.load('current', {packages: ['corechart', 'line']});
	google.charts.setOnLoadCallback(drawBasic);

	function functABC(){
		$.ajax({
			url: 'connections.php',
			method: 'GET',
			async: false,
			success: function(response) {
				return response;
			}
		});
	}
	
	function drawBasic() {

		var data = new google.visualization.DataTable();
		data.addColumn('timeofday', 'X');
		data.addColumn('number', 'Aufrufe');

		var request = new XMLHttpRequest();
		request.open('GET', 'connections.php', false);  // `false` makes the request synchronous
		request.send(null);

		if (request.status === 200) {
			var response = request.responseText;
			var responseArr = response.split(";");
			for(i=0; i<responseArr.length; i++) {
				var entryArr = responseArr[i].split(",");
				console.log(entryArr);
				data.addRow([[parseInt(entryArr[0]),parseInt(entryArr[1]),parseInt(entryArr[2])],parseInt(entryArr[3])]);
			}
		}

		var options = {
		hAxis: {
		  title: 'Uhrzeit',
		  titleTextStyle: { italic: false }
		},
		vAxis: {
		  title: 'Anzahl Aufrufe',
		  titleTextStyle: { italic: false },
		  viewWindowMode: "explicit", 
		  viewWindow:{ min: 0 }
		}
		};

		var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

		chart.draw(data, options);
	}
</script>
</head>
<body>
<?php 
	echo "Session-ID: ".session_id()."<br>"; 
	$date = new DateTime();
	echo "Anfrage um: ".$date->format("H:i:s")." Uhr";
?>
<div style="width:80%;height:60%" id="chart_div"></div>
</body>
</html>
<?php session_destroy(); ?>