<?php
// fetch_courses.php - Returns all courses as JSON
header('Content-Type: application/json');
session_start();
include "config.php";

// Optional: only allow admin to access
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
    exit;
}

// Fetch courses from DB
$sql = "SELECT * FROM courses ORDER BY created_at DESC";
$result = $conn->query($sql);

$courses = [];

if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $courses[] = $row;
    }
}

echo json_encode(['status' => 'success', 'courses' => $courses]);

$conn->close();
?>