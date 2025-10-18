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

        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        $video_url = $data['video_url'] ?? '';
        $image_url = $data['image_url'] ?? '';
        $category = $data['category'] ?? '';

        if (empty($title) || empty($description) || empty($category)) {
            echo json_encode(['status' => 'error', 'message' => 'All required fields must be filled']);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO courses (title, description, video_url, image_url, category) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $description, $video_url, $image_url, $category);

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

        $course_id = $data['course_id'] ?? '';
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        $video_url = $data['video_url'] ?? '';
        $image_url = $data['image_url'] ?? '';
        $category = $data['category'] ?? '';

        if (empty($course_id) || empty($title) || empty($description)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE courses SET title=?, description=?, video_url=?, image_url=?, category=? WHERE course_id=?");
        $stmt->bind_param("sssssi", $title, $description, $video_url, $image_url, $category, $course_id);

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
        $course_id = $data['course_id'] ?? '';

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
