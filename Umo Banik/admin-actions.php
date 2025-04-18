<?php
session_start();
// Check if user is admin
if(!isset($_SESSION['admin'])) {
    header('Location: admin-login.php');
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
    
    $checkQuery = "SELECT * FROM destinations WHERE name = :name";
    $check = $conn->prepare($checkQuery);
    $check->bindParam(':name', $destName, PDO::PARAM_STR);
    $check->execute();
    
    if($check->rowCount() > 0) {
        // Destination already exists
        echo '<script>
            alert("Error: Destination \'' . $destName . '\' already exists. Try adding another destination!!");
            window.location.href = "admin.php";
        </script>';
        exit();
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
            echo '<script>
                alert("Destination \'' . $destName . '\' added successfully!");
                window.location.href = "admin.php";
            </script>';
            exit();
        } else {
            echo '<script>
                alert("Error: Failed to add destination.");
                window.location.href = "admin.php";
            </script>';
            exit();
        }
    }
    
}