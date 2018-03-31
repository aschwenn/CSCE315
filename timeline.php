<?php
	require_once("analyticsfunctions.php");
	date_default_timezone_set('US/Central');

	/* CONSTANTS */
	$CHART_HEIGHT = 500;
	$CHART_WIDTH = 1500;

	/* Database */
    $database = "XXXXX";

	// Receive form data
	$start = date("Y-m-d", strtotime("yesterday")); // yesterday
	$end = date('Y-m-d'); // today
	$ticks = 0; // number of x-axis ticks on chart
	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$start = $_POST["start"];
		$end = $_POST["end"];
		$ticks = $_POST["ticks"]; // 0: by hour, 1: by day
	}

	// Query database based on form input
	$sql = "SELECT * FROM `$database`.`Project1` WHERE `Timestamp` BETWEEN '" . $start . " 00:00:00.000000' AND '" . $end . " 23:59:59.999999'";
	$result = $conn->query($sql);
	$rawdata = getTimelineData($result,$start,$end,$ticks);
	$tdata = formatTimelineData($rawdata);
?>

<html>
	<head>
	<!-- Using styles.css for all style design -->
	<link rel="stylesheet" href="style.css">

	<!-- Timeline graph will utilize a Google Charts line graph -->
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<script type="text/javascript">
			google.charts.load('current', {'packages':['bar']});
			google.charts.setOnLoadCallback(drawChart);

			function drawChart()
			{
				var data = new google.visualization.arrayToDataTable([
					['Date and Time', 'Number of Visitors'],
					<?php 
						// JavaScript code is generated to fill the data array
						echo $tdata;
					?>
				]);

				var options = {
					legend: { position: 'none' },
					vAxis: { title: "Number of Visitors" },
					hAxis: { title: "Date and Time" }
				};

				var chart = new google.charts.Bar(document.getElementById('timeline'));

				chart.draw(data, google.charts.Bar.convertOptions(options));
			}
		</script>
	</head>
	<body>
		<!-- Receive input from user to manipulate timeline -->
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
			View data from <input type="date" name="start" value="<?php echo date("Y-m-d", strtotime("yesterday")); ?>"> to <input type="date" name="end" value="<?php echo date('Y-m-d'); ?>">
			<select name="ticks">
				<option value="0">by hour</option>
				<option value="1">by day</option>
			</select>
			<input type="submit" name="submit" value="Submit">
			<br>
		</form>

		<br><br>
		<?php
			echo "<b><h3>Displaying traffic from " . $start . " to " . $end . "</b></h3><br>";
			printStats($rawdata,$ticks);
		?>

		<!-- div block is required to display a Google Chart -->
		<center><div id="timeline" style="width:<?php echo ($CHART_WIDTH); ?>px; height:<?php echo ($CHART_HEIGHT); ?>px;"></div></center>
	</body>
</html>