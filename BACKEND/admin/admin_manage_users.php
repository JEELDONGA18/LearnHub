<?php
// admin_manage_users.php
include '../config.php';
header('Content-Type: application/json');
session_start();

// Only admin can access
// if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
//     echo json_encode(['status' => 'error', 'message' => 'Access denied']);
//     exit;
// }
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
    exit;
}

$action = $_GET['action'] ?? '';

switch($action) {

    // ================= FETCH ALL USERS =================
    case 'fetch':
        $sql = "SELECT user_id, name, email, role, created_at FROM users ORDER BY user_id DESC";
        $result = $conn->query($sql);

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        echo json_encode(['status' => 'success', 'data' => $users]);
        break;

    // ================= DELETE USER =================
    case 'delete':
        $data = json_decode(file_get_contents("php://input"), true);
        $user_id = $data['user_id'] ?? '';

        if (empty($user_id)) {
            echo json_encode(['status' => 'error', 'message' => 'User ID missing']);
            exit;
        }

        // Optional: Prevent admin from deleting themselves
        if ($user_id == $_SESSION['user_id']) {
            echo json_encode(['status' => 'error', 'message' => 'You cannot delete yourself']);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'User deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error deleting user: ' . $conn->error]);
        }

        $stmt->close();
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}

$conn->close();
?>