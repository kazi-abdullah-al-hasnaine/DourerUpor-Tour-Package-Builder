<?php
session_start();
// Check if user is admin
if(!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

require_once('db_connection/db.php');
$db = Database::getInstance();
$conn = $db->getConnection();


if(isset($_POST['dest-button'])) {
    $destName = trim($_POST['dest']);
    $destType = $_POST['destType'];
    $destCost = isset($_POST['destCost']) ? (float)$_POST['destCost'] : 0;
    $destCountry = isset($_POST['destCountry']) ? trim($_POST['destCountry']) : 'Bangladesh';
    
    // Check if destination already exists
    $checkQuery = "SELECT * FROM destinations WHERE name = :name";
    $check = $conn->prepare($checkQuery);
    $check->bindParam(':name', $destName, PDO::PARAM_STR);
    $check->execute();
    
    if($check->rowCount() > 0) {
        // Destination already exists
        $_SESSION['admin_message'] = "Error: Destination '$destName' already exists.";
        $_SESSION['admin_message_type'] = "error";
    } else {
        // Add new destination
        $insertQuery = "INSERT INTO destinations (name, country, type, cost) 
                        VALUES (:name, :country, :type, :cost)";
        $insert = $conn->prepare($insertQuery);
        $insert->bindParam(':name', $destName, PDO::PARAM_STR);
        $insert->bindParam(':country', $destCountry, PDO::PARAM_STR);
        $insert->bindParam(':type', $destType, PDO::PARAM_STR);
        $insert->bindParam(':cost', $destCost, PDO::PARAM_STR);
        
        if($insert->execute()) {
            $_SESSION['admin_message'] = "Destination '$destName' added successfully!";
            $_SESSION['admin_message_type'] = "success";
        } else {
            $_SESSION['admin_message'] = "Error: Failed to add destination.";
            $_SESSION['admin_message_type'] = "error";
        }
    }
    
    header('Location: admin.php');
    exit();
}