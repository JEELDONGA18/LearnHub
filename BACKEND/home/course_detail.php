<?php
// course_detail.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include '../config.php';

// Check if course_id is provided
if (isset($_GET['id'])) {
    $course_id = $_GET['id'];

    // Correct column name
    $stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $course = $result->fetch_assoc();
        echo json_encode(["status" => "success", "course" => $course]);
    } else {
        echo json_encode(["status" => "error", "message" => "Course not found"]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Course ID not provided"]);
}

$conn->close();
?>
