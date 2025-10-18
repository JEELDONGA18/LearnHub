<?php
// submit_feedback.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include '../config.php';

// Get POST data from frontend
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['user_id']) && isset($data['course_id']) && isset($data['rating']) && isset($data['comment'])) {
    $user_id = $data['user_id'];
    $course_id = $data['course_id'];
    $rating = $data['rating'];
    $comment = $data['comment'];

    // Insert feedback
    $stmt = $conn->prepare("INSERT INTO feedback (user_id, course_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiis", $user_id, $course_id, $rating, $comment);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Feedback submitted successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to submit feedback"]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Missing required data"]);
}

$conn->close();
?>