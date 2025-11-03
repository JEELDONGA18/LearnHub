<?php
include '../config.php';
session_start();

header('Content-Type: application/json');

// ✅ Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// ✅ Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Check course_id in POST
if (empty($_POST['course_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Course ID missing']);
    exit;
}

$course_id = intval($_POST['course_id']);

// ✅ Verify the course exists
$check_course = $conn->prepare("SELECT course_id FROM courses WHERE course_id = ?");
$check_course->bind_param("i", $course_id);
$check_course->execute();
$course_result = $check_course->get_result();

if ($course_result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Course not found']);
    $check_course->close();
    exit;
}
$check_course->close();

// ✅ Check if already enrolled
$check = $conn->prepare("SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?");
$check->bind_param("ii", $user_id, $course_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Already enrolled in this course']);
    $check->close();
    exit;
}
$check->close();

// ✅ Enroll user (default progress = 0)
$stmt = $conn->prepare("INSERT INTO enrollments (course_id, user_id, progress) VALUES (?, ?, 0)");
$stmt->bind_param("ii", $course_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Course enrolled successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>