<?php
// admin_manage_courses.php
include '../config.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {

    // ================= FETCH ALL COURSES =================
    case 'fetch':
        $sql = "SELECT * FROM courses ORDER BY course_id DESC";
        $result = $conn->query($sql);

        $courses = [];
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }

        echo json_encode(['status' => 'success', 'data' => $courses]);
        break;

    // ================= ADD NEW COURSE =================
    case 'add':
        $data = json_decode(file_get_contents("php://input"), true);

        $title = trim($data['title'] ?? '');
        $description = trim($data['description'] ?? '');
        $youtube_video_id = trim($data['youtube_video_id'] ?? '');
        $youtube_playlist_id = trim($data['youtube_playlist_id'] ?? '');
        $video_count = intval($data['video_count'] ?? 1);
        $image_url = trim($data['image_url'] ?? '');
        $category = trim($data['category'] ?? '');

        if (empty($title) || empty($description) || empty($category)) {
            echo json_encode(['status' => 'error', 'message' => 'All required fields must be filled']);
            exit;
        }

        $stmt = $conn->prepare("
            INSERT INTO courses (title, description, youtube_video_id, youtube_playlist_id, video_count, image_url, category)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssiss", $title, $description, $youtube_video_id, $youtube_playlist_id, $video_count, $image_url, $category);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Course added successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error adding course: ' . $conn->error]);
        }

        $stmt->close();
        break;

    // ================= EDIT EXISTING COURSE =================
    case 'edit':
        $data = json_decode(file_get_contents("php://input"), true);

        $course_id = intval($data['course_id'] ?? 0);
        $title = trim($data['title'] ?? '');
        $description = trim($data['description'] ?? '');
        $youtube_video_id = trim($data['youtube_video_id'] ?? '');
        $youtube_playlist_id = trim($data['youtube_playlist_id'] ?? '');
        $video_count = intval($data['video_count'] ?? 1);
        $image_url = trim($data['image_url'] ?? '');
        $category = trim($data['category'] ?? '');

        if (empty($course_id) || empty($title) || empty($description)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
            exit;
        }

        $stmt = $conn->prepare("
            UPDATE courses 
            SET title=?, description=?, youtube_video_id=?, youtube_playlist_id=?, video_count=?, image_url=?, category=?
            WHERE course_id=?
        ");
        $stmt->bind_param("ssssiisi", $title, $description, $youtube_video_id, $youtube_playlist_id, $video_count, $image_url, $category, $course_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Course updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error updating course: ' . $conn->error]);
        }

        $stmt->close();
        break;

    // ================= DELETE COURSE =================
    case 'delete':
        $data = json_decode(file_get_contents("php://input"), true);
        $course_id = intval($data['course_id'] ?? 0);

        if (empty($course_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Course ID missing']);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM courses WHERE course_id=?");
        $stmt->bind_param("i", $course_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Course deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error deleting course: ' . $conn->error]);
        }

        $stmt->close();
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}

$conn->close();
?>