<?php
	/* Contains all analytics functions to be referenced in webserver files */

	// Generates a random table for testing purposes
	function generateTable($conn, $database, $tablename, $numEntries, $date)
	{
		// Generate and insert random data
		for ($i = 0; $i < $numEntries; $i++)
		{
			// Generate day of the week
			$x = rand(0,6);
			switch($x)
			{
				case 0:
					$day = "Monday";
					break;
				case 1:
					$day = "Tuesday";
					break;
				case 2:
					$day = "Wednesday";
					break;
				case 3:
					$day = "Thursday";
					break;
				case 4:
					$day = "Friday";
					break;
				case 5:
					$day = "Saturday";
					break;
				case 6:
					$day = "Sunday";
					break;
				default:
					echo "<b>This should not be possible.</b>";
			}

			// Generate hour of the day
			// Sum 18 random integers from 0 to 1 and add 6 to create a (somewhat) normal distribution
			$hour = 0;
			for ($j = 0; $j < 18; $j++){
				$temp = rand(0,1);
				$hour += $temp;
			}
			$hour += 6;

			// Insert random data into database
			if ($date == NULL)
			{
				$sql = "INSERT INTO `" . $database . "`.`" . $tablename . "` (`ID`, `Timestamp`, `Time`, `DayOfWeek`) VALUES (NULL, CURRENT_TIMESTAMP, '" . $hour . ":00:00', '" . $day . "');";
			}
			else
			{
				$sql = "INSERT INTO `" . $database . "`.`" . $tablename . "` (`ID`, `Timestamp`, `Time`, `DayOfWeek`) VALUES (NULL, '" . $date . " " . $hour . ":00:00', '" . $hour . ":00:00', '" . $day . "');";
			}
			// Error handling
			if (!($conn->query($sql) === TRUE))
			{
				echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}
	}

	// Creates a realistic rec usage history to test the charts and for display purposes
	function generateHistory($conn, $database, $tablename)
	{
		// Note that this function can and will create hundreds of thousands of entries into the database, and will require a few second to complete

		// Create a history exactly one year back to demonstrate potential results
  		$end = date('Y-m-d'); // today
  		$start = (substr($end,0,4) - 1) . substr($end,4,6); // a year ago

  		$TRAFFIC = 20; // How many visitors may come in on an average day

  		// Multipliers will be used to approximate different levels of traffic

  		// Initially clear the table
  		$sql = "DELETE FROM `$database`.`Project1`";
        if ($conn->query($sql) === TRUE)
        {
        	echo "Record cleared successfully.";
        }
        else
        {
        	echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $currentdate = $start;
        for ($i = 0; $i < 365; $i++)
        {
        	$month = substr($currentdate,5,2);
        	$day = substr($currentdate,8,2);
        	$multiplier = 0;
        	if ($month == "01")
        	{
        		// January, no class for first few days
        		// "New Year's Resolutioners" cause a spike in traffic
        		if ($day < 15) $multiplier = 0.2;
        		else $multiplier = 1.3;
        	}
        	elseif ($month == "02")
        	{
        		// February, still some lingering resolutioners
        		$multiplier = 1.2;
        	}
        	elseif ($month == "03" || $month == "04")
        	{
        		$multiplier = 1;
        	}
        	elseif ($month == "05")
        	{
        		// May, finals and semester ends
        		if ($day < 9) $multiplier = 0.7;
        		elseif ($day < 15) $multiplier = 0.5;
        		else $multiplier = 0.4;
        	}
        	elseif ($month == "06" || $month == "07")
        	{
        		$multiplier = 0.3;
        	}
        	elseif ($month == "08")
        	{
        		// August, school starts again
        		// Rush of freshmen
        		if ($day < 20) $multiplier = 0.4;
        		else $multiplier = 1.3;
        	}
        	elseif ($month == "09")
        	{
        		$multiplier = 1.2;
        	}
        	elseif ($month == "10")
        	{
        		$multiplier = 1;
        	}
        	elseif ($month == "11")
        	{
        		// November, Thanksgiving
        		if ($day == 25 || $day == 26 || $day == 27 || $day == 28) $multiplier = 0.7;
        		else $multiplier = 1;
        	}
        	else
        	{
        		// December, finals and semester ending
        		if ($day < 13) $multiplier = 0.7;
        		else $multiplier = 0.2;
        	}

        	// Send data to the database
        	generateTable($conn, $database, $tablename, floor($TRAFFIC * $multiplier), $currentdate);

        	$currentdate = getNextDate($currentdate);
        }
	}

	// Records frequency of students per day of the week based on query data
	function getFreqPerDay($result)
	{
		$freq = array(0,0,0,0,0,0,0); // create array to contain frequencies
		while ($row = mysqli_fetch_array($result))
		{
			$day = $row["DayOfWeek"];
			switch($day)
			{
				case "Monday":
					$freq[0] = $freq[0] + 1;
					break;
				case "Tuesday":
					$freq[1] = $freq[1] + 1;
					break;
				case "Wednesday":
					$freq[2] = $freq[2] + 1;
					break;
				case "Thursday":
					$freq[3] = $freq[3] + 1;
					break;
				case "Friday":
					$freq[4] = $freq[4] + 1;
					break;
				case "Saturday":
					$freq[5] = $freq[5] + 1;
					break;
				case "Sunday":
					$freq[6] = $freq[6] + 1;
					break;
				default:
					echo "<b>This should not be possible.</b>";
			}
		}
		return $freq;
	}

	// Records frequency of students per month based on query data
	function getFreqPerMonth($result)
	{
		$freq = array(0,0,0,0,0,0,0,0,0,0,0,0);
		while ($row = mysqli_fetch_array($result))
		{
			$month = (string)$row["Timestamp"];
			$month = intval(substr($month,5,2)) - 1; // grab month values from timestamp
			if ($month < 12) $freq[$month] += 1; // update frequency array accordingly
		}
		return $freq;
	}

	// Records frequency of students per hour based on query data
	function getFreqPerHour($result)
	{
		// Rec hours are 6am-12am, therefore the first index represents 6am
		$freq = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		while ($row = mysqli_fetch_array($result))
		{
			$hour = (string)$row["Time"];
			$hour = intval(substr($hour,0,2)) - 6; // grab just the hour values
			if ($hour < 18) $freq[$hour] += 1;
		}
		return $freq;
	}

	// Returns a list of tuples containing time/date and count for all requested datapoints
	function getTimelineData($result,$start,$end,$ticks)
	{
		// $ticks refers to counting by hour (0) or by day (1)
		$data = array();
		/*
			Template for pushing to an array of tuples:
			$data[] = array("datetime",count);
		*/
		if(!$ticks)
		{
			// by hour
			// fill data array with zeros for each hour
			$currentdate = $start;
			while ($currentdate != $end)
			{
				for ($i = 6; $i < 10; $i++)
				{
					$data[] = array($currentdate .  " 0" . $i, 0);
				}
				for ($i = 10; $i < 23; $i++)
				{
					$data[] = array($currentdate .  " " . $i, 0);
				}
				$currentdate = getNextDate($currentdate);
			}
			// loop once more for the final date
			for ($i = 6; $i < 10; $i++)
			{
				$data[] = array($currentdate .  " 0" . $i, 0);
			}
			for ($i = 10; $i < 23; $i++)
			{
				$data[] = array($currentdate .  " " . $i, 0);
			}

			// start incrementing values in tuples
			while ($row = mysqli_fetch_array($result))
			{
				$time = substr($row["Timestamp"],0,10) . " " . substr($row["Time"],0,2); // get date and hour of entry
				for ($i = 0; $i < sizeof($data); $i++)
				{
					if ($data[$i][0] == $time) $data[$i][1] += 1;
				}
			}
		}
		else
		{
			// by day
			// fill data array with zeros for each day
			$currentdate = $start;
			while ($currentdate != $end)
			{
				$data[] = array($currentdate,0);
				$currentdate = getNextDate($currentdate);
			}
			$data[] = array($currentdate,0);

			// start incrementing values in tuples
			while ($row = mysqli_fetch_array($result))
			{
				$time = substr($row["Timestamp"],0,10); // get date and hour of entry
				for ($i = 0; $i < sizeof($data); $i++)
				{
					if ($data[$i][0] == $time) $data[$i][1] += 1;
				}
			}
		}
		return $data;
	}

	// Gets next date based on current date
	function getNextDate($date)
	{
		// Timestamp format: 2018-03-20
		$month = substr($date,5,2);
		$day = substr($date,8,2);
		$year = substr($date,0,4);
		if ($month == 1 || $month == 3 || $month == 5 || $month == 7 || $month == 8 || $month == 10)
		{
			// 31 days
			if ($day == 31)
			{
				if ($month >= 9) return $year . "-" . ($month + 1) . "-01";
				else return $year . "-0" . ($month + 1) . "-01";
			}
			if ($day < 31)
			{
				if ($day >= 9) return $year . "-" . $month . "-" . ($day + 1);
				else return $year . "-" . $month . "-0" . ($day + 1);
			}
		}
		if ($month == 4 || $month == 6 || $month == 9 || $month == 11)
		{
			// 30 days
			if ($day == 30)
			{
				if ($month >= 9) return $year . "-" . ($month + 1) . "-01";
				else return $year . "-0" . ($month + 1) . "-01";
			}
			if ($day < 30)
			{
				if ($day >= 9) return $year . "-" . $month . "-" . ($day + 1);
				else return $year . "-" . $month . "-0" . ($day + 1);
			}
		}
		if ($month == 2)
		{
			// 28 days
			if ($day == 28)
			{
				if ($month >= 9) return $year . "-" . ($month + 1) . "-01";
				else return $year . "-0" . ($month + 1) . "-01";
			}
			if ($day < 28)
			{
				if ($day >= 9) return $year . "-" . $month . "-" . ($day + 1);
				else return $year . "-" . $month . "-0" . ($day + 1);
			}
		}
		if ($month == 12)
		{
			// next year
			if ($day == 31)
			{
				return ($year + 1) . "-01-01";
			}
			if ($day < 31)
			{
				if ($day >= 9) return $year . "-" . $month . "-" . ($day + 1);
				else return $year . "-" . $month . "-0" . ($day + 1);
			}
		}
		return "0000-00-00";
	}

	// Converts from military time to standard time
	function convertTime($timestamp)
	{
		$date = substr($timestamp,0,10);
		$time = substr($timestamp,11,2);
		if ($time < 10)
		{
			return $date . " " . substr($time,1,1) . " AM";
		}
		if ($time < 12)
		{
			return $date . " " . $time . " AM";
		}
		if ($time == 12)
		{
			return $date . " " . $time . " PM";
		}
		if ($time > 12)
		{
			return $date . " " . ($time - 12) . " PM";
		}
	}

	// Formats timeline data to be used in Google Charts (javascript array format)
	function formatTimelineData($data)
	{
		// Input is a list of tuples which each contain [Date/Time, Count]
		$sdata = ""; // put all data in a string
		for ($i = 0; $i < count($data); $i++)
		{
			$sdata .= "['" . convertTime($data[$i][0]) . "'," . $data[$i][1] . "],";
		}
		return $sdata;
	}

	// Prints out average, minimum, maximum
	function printStats($data,$ticks)
	{
		// Initialize all
		$sum = 0;
		$min = $data[0]; // save min, max as tuples
		$max = $min;
		// Check every value
		for ($i = 0; $i < sizeof($data); $i++)
		{
			$sum += $data[$i][1];
			if ($data[$i][1] < $min[1]) $min = $data[$i];
			if ($data[$i][1] > $max[1]) $max = $data[$i];
		}
		$average = floor($sum / sizeof($data));

		// Printing
		if (!$ticks)
		{
			echo "<em>Average visitors per hour:</em> " . $average . "<br>\n";
			echo "<em>Maximum number of visitors:</em> " . $max[1] . " --- <em>Day and hour:</em> " . convertTime($max[0]) . "<br>\n";
			echo "<em>Minimum number of visitors:</em> " . $min[1] . " --- <em>Day and hour:</em> " . convertTime($min[0]) . "<br>\n";
		}
		if ($ticks)
		{
			echo "<em>Average visitors per day:</em> " . $average . "<br>\n";
			echo "<em>Maximum number of visitors:</em> " . $max[1] . " --- <em>Day:</em> " . convertTime($max[0]) . "<br>\n";
			echo "<em>Minimum number of visitors:</em> " . $min[1] . " --- <em>Day:</em> " . convertTime($min[0]) . "<br>\n";
		}
	}
?>