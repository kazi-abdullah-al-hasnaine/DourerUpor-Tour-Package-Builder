<?php 
session_start(); 
require_once('C:\xampp2\htdocs\website\DourerUpor-Tour-Package-Builder\Umo Banik\db_connection\db.php'); 
$db = Database::getInstance(); 
$conn = $db->getConnection();  

// Get package ID from form submission
$package_id = isset($_POST['package_id']) ? intval($_POST['package_id']) : 0;
// Create the redirect URL with package ID
$redirect_url = "package.php?id=" . $package_id;

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo '<script>
        alert("You must be logged in to submit a review");
        window.location.href = "' . $redirect_url . '";
    </script>';
    exit; 
}  

// Check if form was submitted
if (isset($_POST['submit-review-btn'])) {
    $email = $_SESSION['email'];
    $user_rating = $_POST['rating'];
    $user_review = $_POST['review'];
    
    // Validate inputs
    if (empty($user_review)) {
        echo '<script>
            alert("Please fill out this field!");
            window.location.href = "' . $redirect_url . '";
        </script>';
        exit;
    }
    
    // Get the logged-in user's ID
    $stmt = $conn->prepare("SELECT id FROM user WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $userId = $user['id'];
        
        // Check if user already submitted a review for this package
        $stmt = $conn->prepare("SELECT review_id FROM reviews WHERE user_id = :user_id AND package_id = :package_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':package_id', $package_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo '<script>
                alert("You have already submitted a review for this package!");
                window.location.href = "' . $redirect_url . '";
            </script>';
            exit;
        } else {
            // Insert the review using PDO
            $stmt = $conn->prepare("INSERT INTO reviews (package_id, rating, review, user_id, review_publish_time)
                                  VALUES (:package_id, :rating, :review, :user_id, NOW())");
            $stmt->bindParam(':package_id', $package_id);
            $stmt->bindParam(':rating', $user_rating);
            $stmt->bindParam(':review', $user_review);
            $stmt->bindParam(':user_id', $userId);
            
            if ($stmt->execute()) {
                echo '<script>
                    alert("Review submitted successfully!");
                    window.location.href = "' . $redirect_url . '";
                </script>';
                exit;
            } else {
                echo '<script>
                    alert("Error submitting review. Please try again.");
                    window.location.href = "' . $redirect_url . '";
                </script>';
                exit;
            }
        }
    } else {
        echo '<script>
            alert("User account not found!");
            window.location.href = "' . $redirect_url . '";
        </script>';
        exit;
    }
} else {
    // If the form wasn't submitted properly, redirect back
    header("Location: " . (empty($package_id) ? "package.php" : $redirect_url));
    exit;
}
?>