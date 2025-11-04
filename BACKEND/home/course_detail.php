<?php
// course_detail.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include '../config.php';

if (isset($_GET['id'])) {
    // Fetch one course by ID
    $course_id = intval($_GET['id']);
    $stmt = $conn->prepare("
        SELECT 
            course_id, 
            title, 
            description,
            youtube_video_id, 
            youtube_playlist_id,
            video_count,
            image_url,
            category
        FROM courses 
        WHERE course_id = ?
    ");
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
    // Fetch all courses
    $query = "
        SELECT 
            course_id, 
            title, 
            description, 
            youtube_video_id, 
            youtube_playlist_id,
            video_count,
            image_url,
            category
        FROM courses 
        ORDER BY course_id DESC
    ";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $courses = [];
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
        echo json_encode(["status" => "success", "courses" => $courses]);
    } else {
        echo json_encode(["status" => "error", "message" => "No courses found"]);
    }
}
$conn->close();
?>