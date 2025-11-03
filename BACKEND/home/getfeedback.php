<?php
header('Content-Type: application/json');
include('../config.php');

if (!isset($_GET['course_id'])) {
    echo json_encode(["status" => "error", "message" => "Course ID is required"]);
    exit;
}

$course_id = intval($_GET['course_id']);

$sql = "SELECT f.rating, f.comment, f.submitted_at, u.name AS user_name
        FROM feedback f
        JOIN users u ON f.user_id = u.user_id
        WHERE f.course_id = ?
        ORDER BY f.submitted_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

$feedbacks = [];
while ($row = $result->fetch_assoc()) {
    $feedbacks[] = $row;
}

if (count($feedbacks) > 0) {
    echo json_encode(["status" => "success", "feedbacks" => $feedbacks]);
} else {
    echo json_encode(["status" => "success", "feedbacks" => []]);
}