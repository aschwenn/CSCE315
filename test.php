<?php
    include("analyticsfunctions.php");
    date_default_timezone_set('US/Central');

    // ALL TEST FUNCTIONS FOR THE WEBSERVER

    $servername = "database.cse.tamu.edu";
    $database = "XXXXX";
    $username = "XXXXX";
    $tablename = "Project1";
    $password = "XXXXX";

    // Checks for a successful connection to database
    function testConnection($servername, $username, $password)
    {
        // Create connection (object-oriented)
        $conn = new mysqli($servername, $username, $password);

        // Check that connection was established
        if ($conn->connect_error)
        {
            echo("ERROR: could not connect to $servername: " . $conn->connect_error);
        }
        else
        {
            echo "Successfully connected to $servername.";
        }
        echo "<br>\n";

        return $conn;
    }

    // Checks that database can be cleared
    function testClear($conn, $database)
    {
        $sql = "DELETE FROM `$database`.`Project1`";
        if ($conn->query($sql) === TRUE)
        {
            echo "Table cleared successfully.";
        }
        else
        {
            echo "ERROR: " . $sql . "<br>" . $conn->error;
        }
        echo "<br>\n";
    }

    // Checks that data can be inserted
    function testInsert($conn, $database)
    {
        $sql = "INSERT INTO `$database`.`Project1` (`ID`, `Timestamp`, `Time`, `DayOfWeek`) VALUES (NULL, CURRENT_TIMESTAMP, '12:00:00', 'Monday');";
        if ($conn->query($sql) === TRUE)
        {
            echo "New record created successfully.";
        }
        else
        {
            echo "ERROR: " . $sql . "<br>" . $conn->error;
        }
        echo "<br>\n";
    }

    // Checks that data can be queried/viewed
    function testQuery($conn, $database)
    {
        $sql = "SELECT * FROM `$database`.`Project1` WHERE `ID`=(SELECT max(`ID`) FROM (SELECT * FROM `$database`.`Project1` AS `mytable`) `test`)";
        if ($conn->query($sql))
        {
            echo "Last record queried successfully.";
        }
        else
        {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        echo "<br>\n";
    }
    
    // Prints "today" and "yesterday"
    function testCurrentDate()
    {
        echo "Yesterday: " . date("Y-m-d", strtotime("yesterday")) . "<br>\n"; // yesterday
        echo "Today: " . date('Y-m-d') . "<br>\n"; // today
    }

    // Prints expected next day
    function testTomorrow()
    {
        echo "Tomorrow: " . getNextDate(date('Y-m-d')) . "<br>\n";
    }

    // Checks that time is converted from military to standard correctly
    function testTimeConversion()
    {
        if (convertTime("2018-01-01 18") === "2018-01-01 6 PM")
        {
            echo "Time converted correctly.<br>\n";
        }
        else echo "ERROR: Incorrect time conversion.";
    }

    /* Running tests */
    echo "Running all tests: <br><br>\n";
    $conn = testConnection($servername, $username, $password);
    testClear($conn, $database);
    testInsert($conn, $database);
    testQuery($conn, $database);
    testCurrentDate();
    testTomorrow();
    testTimeConversion();
    echo "<br>Tests completed.";
?>