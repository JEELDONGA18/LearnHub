<?php
header('Content-Type: application/json');
session_start();
include('../config.php'); // ✅ Adjust path if needed

// Prevent HTML warnings from breaking JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

// ✅ Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

$course_id = $input['course_id'] ?? null;
$rating = $input['rating'] ?? null;
$comment = $input['feedback'] ?? null; // Frontend sends "feedback"
$user_id = $_SESSION['user_id'];

// ✅ Validate input
if (!$course_id || !$rating || !$comment) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

// ✅ Insert feedback
$stmt = $conn->prepare("INSERT INTO feedback (user_id, course_id, rating, comment, submitted_at)
                        VALUES (?, ?, ?, ?, NOW())");
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("iiis", $user_id, $course_id, $rating, $comment);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Feedback submitted successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>