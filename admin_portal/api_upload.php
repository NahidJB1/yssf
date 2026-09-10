<?php
// admin_portal/api_upload.php
require_once 'auth.php';
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    $consultation_id = intval($_POST['consultation_id'] ?? 0);
    $file = $_FILES['document'];
    
    if ($file['error'] === UPLOAD_ERR_OK && $consultation_id > 0) {
        // Ensure uploads directory exists
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Sanitize file name
        $original_name = basename($file['name']);
        $safe_name = preg_replace('/[^a-zA-Z0-9.\-_]/', '_', $original_name);
        $file_name = time() . '_' . $safe_name;
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $stmt = $pdo->prepare("INSERT INTO documents (consultation_id, file_name, file_path) VALUES (?, ?, ?)");
            $stmt->execute([$consultation_id, $original_name, $file_path]);
            log_activity($pdo, get_current_user_id(), "Uploaded document '$original_name' for lead ID: $consultation_id");
        }
    }
    header("Location: student_profile.php?id=" . $consultation_id);
    exit;
}
header("Location: index.php");
exit;
?>

