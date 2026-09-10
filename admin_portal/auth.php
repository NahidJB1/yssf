<?php
// admin_portal/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Helper functions for authorization
function is_super_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin';
}

function get_current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function get_current_username() {
    return $_SESSION['username'] ?? '';
}

function require_super_admin() {
    if (!is_super_admin()) {
        die("Unauthorized access. Super admin privileges required.");
    }
}

// Activity Logging Helper
function log_activity($pdo, $user_id, $action_description) {
    if ($user_id && $action_description) {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action_description) VALUES (?, ?)");
        $stmt->execute([$user_id, $action_description]);
    }
}
?>
