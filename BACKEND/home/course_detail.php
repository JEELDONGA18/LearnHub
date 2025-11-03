<?php
// courses_detail.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include '../config.php';

// Check if an ID is passed in query string
if (isset($_GET['id'])) {
    // Return details of a single course
    $course_id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $course = $result->fetch_assoc();
        echo json_encode([
            "status" => "success",
            "course" => $course
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Course not found"
        ]);
    }

    $stmt->close();

} else {
    // No ID provided → return all courses
    $query = "SELECT * FROM courses ORDER BY course_id DESC";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $courses = [];
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }

        echo json_encode([
            "status" => "success",
            "courses" => $courses
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No courses found"
        ]);
    }
}

$conn->close();
?>