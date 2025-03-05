<?php
$servername = "localhost";
$username = "mlavagna";
$password = "3YRMmzaGlP";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$database = "mlavagna_xtremesoftwaresolutions";

$error = mysql_select_db($database, $conn);

echo "Error? ".$error;

echo "Connected successfully";
?>