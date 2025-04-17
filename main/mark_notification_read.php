<?php
// Start session
session_start();

// Database connection
require_once 'db_connection/db.php';
$db = Database::getInstance();
$conn = $db->getConnection();

$userId = $_SESSION['user_id'] ;

// Mark notifications as read when viewed
if (isset($_POST['mark_read']) && $_POST['mark_read'] == 1) {
    $notificationId = $_POST['notification_id'] ?? 0;
    
    if ($notificationId > 0) {
        $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
        $stmt->execute([$notificationId, $userId]);
        
        echo json_encode(['success' => true]);
        exit;
    }
}

// Default response for invalid requests
echo json_encode(['success' => false]);
?>