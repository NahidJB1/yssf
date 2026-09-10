<?php
// admin_portal/api_assign.php
require_once 'auth.php';
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);
    
    if ($action === 'claim') {
        $user_id = get_current_user_id();
        $stmt = $pdo->prepare("UPDATE consultations SET assigned_to = ? WHERE id = ? AND assigned_to IS NULL");
        $stmt->execute([$user_id, $id]);
        log_activity($pdo, get_current_user_id(), "Claimed lead ID: $id");
        echo json_encode(['success' => true]);
        
    } elseif ($action === 'assign' && is_super_admin()) {
        $user_id = $_POST['user_id'];
        
        if ($user_id === 'NULL') {
            $stmt = $pdo->prepare("UPDATE consultations SET assigned_to = NULL WHERE id = ?");
            $stmt->execute([$id]);
            log_activity($pdo, get_current_user_id(), "Unassigned lead ID: $id");
        } else {
            $stmt = $pdo->prepare("UPDATE consultations SET assigned_to = ? WHERE id = ?");
            $stmt->execute([intval($user_id), $id]);
            log_activity($pdo, get_current_user_id(), "Assigned lead ID: $id to user ID: $user_id");
        }
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Unauthorized or invalid action']);
    }
}
?>

