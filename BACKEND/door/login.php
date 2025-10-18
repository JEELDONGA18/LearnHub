<?php
// login.php - Handles user login with sessions

session_start();
header('Content-Type: application/json'); // JSON response
include "config.php";

// Get POST data
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

// Basic validation
if(empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

// Fetch user from DB
$stmt = $conn->prepare("SELECT user_id, name, email, password, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
    exit;
}

$user = $result->fetch_assoc();
// Verify password
if($password === $user['password']) {
    // ✅ Login successful, store session
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];

    echo json_encode(['status' => 'success', 'role' => $user['role'], 'message' => 'Login successful.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
}

$stmt->close();
$conn->close();
?>