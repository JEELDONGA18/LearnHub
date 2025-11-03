<?php
include '../config.php';
session_start();
header('Content-Type: application/json');

// ✅ Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch enrolled courses with course info
$query = "
    SELECT 
        c.course_id,
        c.title,
        c.description,   
        e.enroll_date, 
        e.progress
    FROM enrollments e
    INNER JOIN courses c ON e.course_id = c.course_id
    WHERE e.user_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

if (count($courses) > 0) {
    echo json_encode(["status" => "success", "courses" => $courses]);
} else {
    echo json_encode(["status" => "empty", "message" => "No enrolled courses found"]);
}

$stmt->close();
$conn->close();
?>