<?php
// admin_manage_contacts.php
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

    // ================= FETCH ALL CONTACTS =================
    case 'fetch':
        $sql = "SELECT contact_id, name, email, message FROM contacts ORDER BY contact_id DESC";
        $result = $conn->query($sql);

        $contacts = [];
        while ($row = $result->fetch_assoc()) {
            $contacts[] = $row;
        }

        echo json_encode(['status' => 'success', 'data' => $contacts]);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}

$conn->close();
?>