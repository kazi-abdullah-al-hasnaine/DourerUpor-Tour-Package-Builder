<?php
session_start();
$_SESSION['current-page'] = 'buildAndShare';
$active_page = 'buildAndShare';

// Database connection
require_once 'db_connection/db.php';
$db = Database::getInstance();
$conn = $db->getConnection();

$conn = Database::getInstance()->getConnection();

// Getting username from DB for navigation bar
if(isset($_SESSION['email']) || isset($_SESSION['admin'])){
    $email = $_SESSION['email'];
    $stmt = $conn->prepare("SELECT name FROM user WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $username = explode(" ", $user['name'])[0];   
}else{
    echo "<script>
    alert('You must login to use this feature!!');
    window.history.back();
    </script>";
}

include "decoration.php";
include "DesignPatterns/PackageBuilder.php";
include "DesignPatterns/PackageObserver.php";
include "DesignPatterns/imageProxy.php";

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Package Details</title>
</head>
<body>
<nav id="navigation" class="special-nav">
                <div class="logo">
                    <h3>DourerUpor</h3>
                </div>
                <div>
                    <ul>
                        <li class="active-page"><a href="home.php" <?php if($active_page == "home") {echo "class='active'";} ?> >Home</a></li>
                        <li><a <?php if($active_page == "popular") {echo "class='active'";} ?> href="popular.php">Popular</a></li>
                        <li><a <?php if($active_page == "explore") {echo "class='active'";} ?> href="">Explore</a></li>
                        <?php if (isset($_SESSION['email'])): ?>
                        <li><a <?php if ($active_page == "buildAndShare") {
                                    echo "class='active'";
                                } ?> href="buildAndShare.php">Build & Share</a></li>       
                        <li><a href="Profile.php"><?php echo $username; ?></a></li>
                        <?php endif ?>
                    </ul>
                </div>
                <div>
                    <?php if (isset($_SESSION['email'])): ?>
                        <form action="./Login/user-actions.php" method="post">
                            <button type ="Submit" class="theme-btn log-out-btn" title="Click to logout" name="log-out-btn">Log out</button>
                        </form>
                    
                    <?php else: ?>
                    <div class="login-btn-container">
                        <a href="./login/login.html">
                            <button class="login-btn theme-btn">Login</button>
                        </a>
                        <a href="./login/login.html">
                        <button class="signup-btn theme-btn">Sign up</button>
                        </a>
                    </div>
                    <?php endif ?>
                </div>
 </nav>
 <?php
include "modules/packageBuildForm.php";
    // include "modules/wishlist.php";
    include "modules/footer.php";
?>
</body>
