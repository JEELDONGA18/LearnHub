<?php
// delete_course.php - Deletes a course by ID
header('Content-Type: application/json');
session_start();
include "config.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
    exit;
}

$course_id = intval($_POST['course_id'] ?? 0);
if($course_id <= 0){
    echo json_encode(['status' => 'error', 'message' => 'Invalid course ID']);
    exit;
}

// Delete the course
$stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
$stmt->bind_param("i", $course_id);
if($stmt->execute()){
    echo json_encode(['status' => 'success', 'message' => 'Course deleted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete course']);
}

$stmt->close();
$conn->close();
?>