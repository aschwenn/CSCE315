<?php
	require_once("analyticsfunctions.php");

	// CONSTANTS
	$CHART_WIDTH = 500;
	$CHART_LENGTH = 400;

	/* Database */
	$database = "XXXXX";
	
	// CHART 1 data fetch
	$sql = "SELECT * FROM `$database`.`Project1`";
	$result = $conn->query($sql);
	$freq1 = getFreqPerMonth($result);

	// CHART 2 data fetch
	$sql = "SELECT * FROM `$database`.`Project1`";
	$result = $conn->query($sql);
	$freq2 = getFreqPerDay($result);

	// CHART 3 data fetch
	$sql = "SELECT * FROM `$database`.`Project1`";
	$result = $conn->query($sql);
	$freq3 = getFreqPerHour($result);

	// Using Google Charts API
?>

<!DOCTYPE html>
<html>
	<head>
		<!-- Using styles.css for all style design -->
		<link rel="stylesheet" href="style.css">

		<!-- The first three graphs are aggregates and will be bar graphs -->
		<!-- All Google Charts setup code must be contained in the <head> section -->
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<script type="text/javascript">
			google.charts.load('current', {'packages':['bar']});
			google.charts.setOnLoadCallback(drawChart1);
			google.charts.setOnLoadCallback(drawChart2);
			google.charts.setOnLoadCallback(drawChart3);

			function drawChart1()
			{
				// All data is first saved into a JavaScript array
				var data = new google.visualization.arrayToDataTable([
					['Month','Frequency'],
					['Jan', <?php echo $freq1[0]; ?>],
					['Feb', <?php echo $freq1[1]; ?>],
					['Mar', <?php echo $freq1[2]; ?>],
					['Apr', <?php echo $freq1[3]; ?>],
					['May', <?php echo $freq1[4]; ?>],
					['June', <?php echo $freq1[5]; ?>],
					['July', <?php echo $freq1[6]; ?>],
					['Aug', <?php echo $freq1[7]; ?>],
					['Sept', <?php echo $freq1[8]; ?>],
					['Oct', <?php echo $freq1[9]; ?>],
					['Nov', <?php echo $freq1[10]; ?>],
					['Dec', <?php echo $freq1[11]; ?>],
				]);

				// Any options can be set here including dimensions and titles
				var options = {
					width: <?php echo $CHART_WIDTH; ?>,
					height: <?php echo $CHART_LENGTH; ?>,
					legend: { position: 'none' },
					vAxis: { title: "Total Number of Visitors" },
					hAxis: { title: "Month" },
					bar: { groupWidth: "90%" }
				};

				// The chart is named 'chart1' -- this name will be used to display later
				var chart = new google.charts.Bar(document.getElementById('chart1'));

				// Draw the chart based on the specified data and options
				chart.draw(data, google.charts.Bar.convertOptions(options));
			};

			function drawChart2()
			{
				var data = new google.visualization.arrayToDataTable([
					['Day','Frequency'],
					['Mon', <?php echo $freq2[0]; ?>],
					['Tues', <?php echo $freq2[1]; ?>],
					['Wed', <?php echo $freq2[2]; ?>],
					['Thurs', <?php echo $freq2[3]; ?>],
					['Fri', <?php echo $freq2[4]; ?>],
					['Sat', <?php echo $freq2[5]; ?>],
					['Sun', <?php echo $freq2[6]; ?>],
				]);

				var options = {
					width: <?php echo $CHART_WIDTH; ?>,
					height: <?php echo $CHART_LENGTH; ?>,
					legend: { position: 'none' },
					vAxis: { title: "Total Number of Visitors" },
					hAxis: { title: "Day of the Week" },
					bar: { groupWidth: "90%" }
				};

				var chart = new google.charts.Bar(document.getElementById('chart2'));
				
				chart.draw(data, google.charts.Bar.convertOptions(options));
			};

			function drawChart3()
			{
				var data = new google.visualization.arrayToDataTable([
					['Hour','Frequency'],
					['6 AM', <?php echo $freq3[0]; ?>],
					['7 AM', <?php echo $freq3[1]; ?>],
					['8 AM', <?php echo $freq3[2]; ?>],
					['9 AM', <?php echo $freq3[3]; ?>],
					['10 AM', <?php echo $freq3[4]; ?>],
					['11 AM', <?php echo $freq3[5]; ?>],
					['12 PM', <?php echo $freq3[6]; ?>],
					['1 PM', <?php echo $freq3[7]; ?>],
					['2 PM', <?php echo $freq3[8]; ?>],
					['3 PM', <?php echo $freq3[9]; ?>],
					['4 PM', <?php echo $freq3[10]; ?>],
					['5 PM', <?php echo $freq3[11]; ?>],
					['6 PM', <?php echo $freq3[12]; ?>],
					['7 PM', <?php echo $freq3[13]; ?>],
					['8 PM', <?php echo $freq3[14]; ?>],
					['9 PM', <?php echo $freq3[15]; ?>],
					['10 PM', <?php echo $freq3[16]; ?>],
					['11 PM', <?php echo $freq3[17]; ?>],
				]);

				var options = {
					width: <?php echo $CHART_WIDTH; ?>,
					height: <?php echo $CHART_LENGTH; ?>,
					legend: { position: 'none' },
					vAxis: { title: "Total Number of Visitors" },
					hAxis: { title: "Hour of the Day" },
					bar: { groupWidth: "90%" }
				};

				var chart = new google.charts.Bar(document.getElementById('chart3'));

				chart.draw(data, google.charts.Bar.convertOptions(options));
			};
		</script>
	</head>
	<body>
		<center>
		<table cellpadding="10">
			<!-- Titles -->
			<tr>
				<td><center>Frequency Per Month</center></td>
				<td><center>Frequency Per Day of the Week</center></td>
				<td><center>Frequency Per Hour</center></td>
			</tr>
			<!-- Charts -->
			<tr>
				<!-- div blocks are required to display charts; use the name previously set -->
				<td><div id="chart1" style="width:<?php echo $CHART_WIDTH; ?>px; height:<?php echo $CHART_HEIGHT; ?>px;"></div></td>
				<td><div id="chart2" style="width:<?php echo $CHART_WIDTH; ?>px; height:<?php echo $CHART_HEIGHT; ?>px;"></div></td>
				<td><div id="chart3" style="width:<?php echo $CHART_WIDTH; ?>px; height:<?php echo $CHART_HEIGHT; ?>px;"></div></td>
			</tr>
		</table>
		</center>
	</body>
</html>