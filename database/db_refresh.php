<?php
// Database configuration
$host = "localhost"; // Hostinger usually uses localhost for MySQL
$username = "u845324049_lkjohn"; // Replace with your MySQL username
$password = "MyAcademy12#"; // Replace with your MySQL password
$database = "u845324049_lkacademy"; // Replace with the desired database name
$sqlFilePath = "u845324049_lkacademy"; // Path to your SQL file

// Check if the confirmation form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    try {
        // Connect to MySQL server
        $conn = new mysqli($host, $username, $password);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Select the database
        $conn->select_db($database);
		
		// Retrieve and drop all tables in the database
        $tables = [];
        $result = $conn->query("SHOW TABLES");
        while ($row = $result->fetch_array()) {
            $tables[] = $row[0];
        }

        foreach ($tables as $table) {
            $conn->query("DROP TABLE `$table`");
            echo "Dropped table '$table'.<br>";
        }

        echo "All tables cleared from the database '$database'.<br>";

        // Load and execute the SQL file
        if (file_exists($sqlFilePath)) {
            $sqlCommands = file_get_contents($sqlFilePath);
            if ($conn->multi_query($sqlCommands)) {
				echo "Database populated successfully from the SQL file.<br>";
                echo "<a href='../index.php'>Go to Index Page</a><br>";
            } else {
                echo "Error executing SQL file: " . $conn->error . "<br>";
            }
        } else {
            echo "SQL file not found: $sqlFilePath<br>";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        // Close connection
        $conn->close();
    }
} else {
    // Show the confirmation form
    echo <<<HTML
    <form method="post">
        <h2>Confirm Database Overwrite</h2>
        <p>Are you sure you want to overwrite the database '$database'? This action will drop the existing database and recreate it with data from the SQL file.</p>
        <button type="submit" name="confirm">Yes, Proceed</button>
        <button type="button" onclick="window.location.href='../index.php';">Cancel</button>
    </form>
HTML;
}
?>
