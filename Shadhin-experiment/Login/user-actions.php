<?php
require_once 'Strategies/LoginContext.php';
$db = Database::getInstance();
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $context = new LoginContext();

    if (isset($_POST["login-button"])) {
        // Normal login
        $context->setStrategy(new NormalLogin());
        $context->executeLogin($_POST);
    }

    if (isset($_POST["registration-button"])) {
        // Handle registration
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
            echo "Registration successful!";
        } else {
            echo "Registration failed!";
        }
    }

    // Handle Google login (data sent via JSON)
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($data["google-login"])) {
        $context->setStrategy(new GoogleLogin());
        $context->executeLogin($data);
    }
}
