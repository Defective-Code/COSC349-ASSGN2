<?php
include "../inc/dbinfo.inc";

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

// Connect to MySQL
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

//Print the values of each parameter for debugging
echo "Device ID: " . htmlspecialchars($_POST['deviceId']) . "<br>";
echo "User Name: " . htmlspecialchars($_POST['userName']) . "<br>";
echo "Start Date: " . htmlspecialchars($_POST['startDate']) . "<br>";
echo "End Date: " . htmlspecialchars($_POST['endDate']) . "<br>";


// Check if POST variables are set
if (!isset($_POST['deviceId'], $_POST['userName'], $_POST['startDate'], $_POST['endDate'])) {
    die("Missing required parameters.");
}


// Proceed with further processing, e.g., storing the data in the database

// Capture form inputs
$deviceId = htmlentities($_POST['deviceId']);
$userName = htmlentities($_POST['userName']);
$startDate = htmlentities($_POST['startDate']);
$endDate = htmlentities($_POST['endDate']);

debug_to_console($deviceId);
debug_to_console($userName);
debug_to_console($startDate);
debug_to_console($endDate);

// Prepare an SQL statement
$stmt = $connection->prepare("INSERT INTO Booking (userName, deviceId, startDate, endDate) VALUES (?, ?, ?, ?)");
$stmt->bind_param("siss", $userName, $deviceId, $startDate, $endDate);

// Execute the statement
if ($stmt->execute()) {
    echo "Booking confirmed successfully.";
} else {
    echo "Error executing query: " . $stmt->error;
}

// Close the statement and the connection
$stmt->close();
mysqli_close($connection);

// Optionally, redirect back to the main page
header("Location: book.php");
exit();
?>
