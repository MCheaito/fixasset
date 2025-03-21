<?php
  ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   error_reporting(E_ALL);

   date_default_timezone_set("America/New_York");

   require('/var/www/source-emr-labalhadi/vendor/autoload.php');
  
	 $dot = Dotenv\Dotenv::createImmutable('/var/www/source-emr-labalhadi/'); //Location of .env
	 $dot->load(); //Load the configuration (Not override, for override use overload() method
	 $dbHost = env('DB_HOST');
	 $dbUser = env('DB_USERNAME');
	 $dbPass = env('DB_PASSWORD');
	 $dbName = env('DB_DATABASE');
  
    $conn =  mysqli_connect($dbHost, $dbUser , $dbPass,$dbName);

   if (!$conn) {
	  die("Connection failed: " . mysqli_connect_error());
	}
	
  
  try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get current date and calculate cutoff date (6 months ago)
    $cutoffDate = date('Y-m-d H:i:s', strtotime('-6 months'));

    // Step 1: Identify tables with 'last_update' column
   $stmtTables = $pdo->prepare("
        SELECT DISTINCT t.table_name
        FROM information_schema.tables t
        JOIN information_schema.columns c ON t.table_schema = c.table_schema
            AND t.table_name = c.table_name
        WHERE t.table_schema = :dbname
            AND t.table_name LIKE '%trg%'
            AND (c.column_name = 'last_update' OR c.column_name = 'date_update')
    ");
    $stmtTables->bindParam(':dbname', $dbName);
    $stmtTables->execute();
    $tables = $stmtTables->fetchAll(PDO::FETCH_COLUMN);

    // Step 2: Delete records older than 6 months from each table
    foreach ($tables as $table) {
     $stmtColumns = $pdo->query("DESCRIBE $table");
    $columns = $stmtColumns->fetchAll(PDO::FETCH_COLUMN);

    // Determine which delete column to use based on available columns
    if (in_array('last_update', $columns)) {
        $deleteColumn = 'last_update';
    } elseif (in_array('date_update', $columns)) {
        $deleteColumn = 'date_update';
    } else {
        // Default to a suitable column name if neither is found
        $deleteColumn = 'id'; // Adjust this based on your table structure
    }

        $stmtDelete = $pdo->prepare("
            DELETE FROM $table
            WHERE $deleteColumn < :cutoffDate
        ");
        $stmtDelete->bindParam(':cutoffDate', $cutoffDate);
        $stmtDelete->execute();
        $rowCount = $stmtDelete->rowCount();
        echo "Deleted $rowCount records from $table where $deleteColumn < $cutoffDate<br>";
    }

    echo "Deletion process completed successfully.";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>