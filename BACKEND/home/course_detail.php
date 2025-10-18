<?php
// course_detail.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Include database config
include 'config.php';

// Check if course_id is provided
if (isset($_GET['id'])) {
    $course_id = $_GET['id'];

    // Prepare SQL query
    $stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if course exists
    if ($result->num_rows > 0) {
        $course = $result->fetch_assoc();
        echo json_encode(["success" => true, "course" => $course]);
    } else {
        echo json_encode(["success" => false, "message" => "Course not found"]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Course ID not provided"]);
}

$conn->close();
?>