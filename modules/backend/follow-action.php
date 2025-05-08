<?php
session_start();
header('Content-Type: application/json');
require_once '../../db_connection/db.php'; // Assuming your DB connection is inside this file

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$email = $_SESSION['email'];
$action = $_POST['action'];
$packageId = $_POST['package_id'];

$db = Database::getInstance();
$conn = $db->getConnection();

// Get the logged-in user's ID
$stmt = $conn->prepare("SELECT id FROM user WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $userId = $user['id'];

    if ($action == 'follow') {
        // Check if the user is already following the package
        $stmt = $conn->prepare("SELECT 1 FROM package_followers WHERE user_id = :user_id AND package_id = :package_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':package_id', $packageId);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Already following']);
            exit;
        }

        // Follow the package
        $stmt = $conn->prepare("INSERT INTO package_followers (user_id, package_id) VALUES (:user_id, :package_id)");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':package_id', $packageId);
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'Followed']);
    } elseif ($action == 'unfollow') {
        // Unfollow the package
        $stmt = $conn->prepare("DELETE FROM package_followers WHERE user_id = :user_id AND package_id = :package_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':package_id', $packageId);
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'Unfollowed']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
}
?>
