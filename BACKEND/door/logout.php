<?php
session_start();

// ✅ Destroy all session data
session_unset();
session_destroy();

// ✅ Return response to frontend
echo json_encode([
    "status" => "success",
    "message" => "Logged out successfully"
]);
?>