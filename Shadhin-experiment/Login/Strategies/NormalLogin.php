<?php
require_once 'LoginStrategy.php';
require_once '../db_connection/db.php';
class NormalLogin implements LoginStrategy {
    public function login($data) {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $email = $data["loginEmail"];
        $password = $data["loginPassword"];

        $stmt = $conn->prepare("SELECT * FROM user WHERE email = :email AND password = :password");
        $stmt->execute(['email' => $email, 'password' => $password]);

        if ($stmt->rowCount() > 0) {
            // echo "Login successful!";
            session_start();
            $_SESSION['email'] = $email;
            header("Location: ../home.php");
        } else {
            echo "Invalid credentials!";
        }
    }
}
