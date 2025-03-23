<?php
require_once '../DB Connection/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST["user_id"];
    $password = $_POST["password"];

    // Get database instance
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Check login credentials
    $stmt = $conn->prepare("SELECT * FROM user WHERE user_id = :user_id AND password = :password");
    $stmt->execute(['user_id' => $user_id, 'password' => $password]);

    if ($stmt->rowCount() > 0) {
        echo "Login successful!";
    } else {
        echo "Invalid credentials!";
    }
}
?>


