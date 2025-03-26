<?php
// Database connection
require_once '../db_connection/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    if (isset($_POST["login-button"])) {
        $email = $_POST["loginEmail"];
        $password = $_POST["loginPassword"];

        // Check login credentials
        $stmt = $conn->prepare("SELECT * FROM user WHERE email = :email AND password = :password");
        $stmt->execute(['email' => $email, 'password' => $password]);

        if ($stmt->rowCount() > 0) {
            session_start();
            $_SESSION['email'] = $email;
            header("Location: ../home.php");
        } else {
            echo "Invalid credentials!";
        }
    }
    
    if (isset($_POST["registration-buttom"])) {
        $name = $_POST["username"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $dob = $_POST["dob"];
        $country = $_POST["country"];

        // Insert user into database
        $stmt = $conn->prepare("INSERT INTO user (name, email, password, dob, country) VALUES (:name, :email, :password, :dob, :country)");
        $success = $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'dob' => $dob,
            'country' => $country
        ]);

        if ($success) {
            header("Location: login.html");
        } else {
            echo "Registration failed!";
        }
    }
    if (isset($_POST["log-out-btn"])){
        session_unset();
        session_destroy();
        header("Location: login.html");

    }
}
?>
