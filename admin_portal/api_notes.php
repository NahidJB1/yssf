<?php
// admin_portal/api_notes.php
require_once 'auth.php';
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $consultation_id = intval($_POST['consultation_id'] ?? 0);
    $note_text = trim($_POST['note_text'] ?? '');
    $user_id = get_current_user_id();
    
    if ($consultation_id > 0 && !empty($note_text) && $user_id) {
        $stmt = $pdo->prepare("INSERT INTO notes (consultation_id, user_id, note_text) VALUES (?, ?, ?)");
        $stmt->execute([$consultation_id, $user_id, $note_text]);
        log_activity($pdo, $user_id, "Added an internal note for lead ID: $consultation_id");
    }
    
    header("Location: student_profile.php?id=" . $consultation_id);
    exit;
}
header("Location: index.php");
exit;
?>

