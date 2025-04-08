<?php 


$active_page = $_SESSION['current-page'];

// Getting username from DB for navigation bar
if(isset($_SESSION['email'])){
    $db = Database::getInstance();
    $conn = $db->getConnection();
    $email = $_SESSION['email'];
    $stmt = $conn->prepare("SELECT name FROM user WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $username = explode(" ", $user['name'])[0];   
}
 


?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="./styles.css">
</head>
<body>
	<section id="hero-section">
        <div class="overlay">
            <nav id="navigation">
                <div class="logo">
                    <h3>DourerUpor</h3>
                </div>
                <div>
                    <ul>
                        <li class="active-page"><a href="home.php" <?php if($active_page == "home") {echo "class='active'";} ?> >Home</a></li>
                        <li><a <?php if($active_page == "popular") {echo "class='active'";} ?> href="popular.php">Popular</a></li>
                        <li><a <?php if($active_page == "explore") {echo "class='active'";} ?> href="">Explore</a></li>
                        <li><a <?php if($active_page == "build&share") {echo "class='active'";} ?> href="modules/buildPackages.php">Build & Share</a></li>
                        <li><a href="">Wishlist</a></li>
                    </ul>
                </div>
                <div>
                    <?php if (isset($_SESSION['email'])): ?>
                        <form action="./Login/user-actions.php" method="post">
                            <button type ="Submit" class="theme-btn log-out-btn" title="Click to logout" name="log-out-btn"><?php echo $username; ?></button>
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
            <div class="hero-body">
                <div class="hero-body-items">
                    <h1><?php echo htmlspecialchars_decode($heroTitle); ?></h1>
                    <button class="theme-btn start-exploring-btn">Start exploring</button>
                    
                </div>
            </div>   
            <div class="hero-search-bar">
                    <div class="search-box-wrapper">
                        <input placeholder="Search your dream destination..." type="text" id="search-box" name="search-box">
                        <button class="search-btn">🔍</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <?php 
       // include "wishlist.php";
    ?>
    
</body>
</html>