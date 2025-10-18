<?php
// config.php - Database connection file

// Database credentials
$host = "127.0.0.1";        // Usually localhost
$db_name = "learnhub";   // Your database name
$username = "root";         // Your DB username
$password = "student";             // Your DB password

// Create connection using mysqli
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set (optional but recommended)
$conn->set_charset("utf8mb4");

?>
