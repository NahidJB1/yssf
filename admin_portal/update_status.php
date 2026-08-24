<?php
session_start();

// Security Check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

    if ($id && in_array($status, ['pending', 'followed_up'])) {
        try {
            $stmt = $pdo->prepare("UPDATE consultations SET status = :status WHERE id = :id");
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success']);
                exit;
            }
        } catch (\PDOException $e) {
            // silent fail for json
        }
    }
}
echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
exit;
