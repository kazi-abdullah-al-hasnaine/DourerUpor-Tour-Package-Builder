<?php
require_once 'Strategies/LoginContext.php';
$db = Database::getInstance();
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $context = new LoginContext();

    $data = json_decode(file_get_contents("php://input"), true); // Always decode the JSON data

    if (isset($data["login-button"])) {
        // Normal login (Handle based on your form data)
        $context->setStrategy(new NormalLogin());
        $context->executeLogin($data);
    }

    if (isset($data["google-login"])) {
        // Google login (data sent via JavaScript)
        $context->setStrategy(new GoogleLogin());
        $context->executeLogin($data);
    }

    if (isset($data["registration-button"])) {
        // Handle registration (this part is the same as before)
        $name = $data["username"];
        $email = $data["email"];
        $password = $data["password"];
        $dob = $data["dob"];
        $country = $data["country"];

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
}
