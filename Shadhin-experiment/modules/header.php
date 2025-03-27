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
                        <li><a <?php if($active_page == "build&share") {echo "class='active'";} ?> href="">Build & Share</a></li>
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


    <!-- ----For wishlist sidebar ----- -->


    <div id="wishlist-sidebar" class="wishlist-sidebar">
        <button id="close-wishlist" class="close-btn">&times;</button>
        <section id="wishlist-container">
            <div class="wishlist-card">
                <h3 class="package-name card-item">Trip to Rajshahi</h3>
                    <div class="card-item">
                        <button class="theme-btn info-btn">Rajshahi</button>
                        <button disabled class="theme-btn info-btn">2 day/s</button>
                        <button disabled class="theme-btn info-btn">4.1⭐</button>
                        <button disabled class="theme-btn info-btn">5💬</button>
                        <button disabled class="theme-btn info-btn offer">Save 100$</button>
                    </div>
                    <p class="card-item package-brief">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue.</p>
                    <div class="card-item">
                        <button class="theme-btn package-explore-btn">Explore</button>
                        <button class="theme-btn package-explore-btn remove-btn">Remove</button>
                    </div>
            </div>
            <div class="wishlist-card">
                <h3 class="package-name card-item">Trip to Sylhet</h3>
                    <div class="card-item">
                        <button class="theme-btn info-btn">Rajshahi</button>
                        <button disabled class="theme-btn info-btn">2 day/s</button>
                        <button disabled class="theme-btn info-btn">4.1⭐</button>
                        <button disabled class="theme-btn info-btn">5💬</button>
                        <button disabled class="theme-btn info-btn offer">Save 100$</button>
                    </div>
                    <p class="card-item package-brief">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue.</p>
                    <div class="card-item">
                        <button class="theme-btn package-explore-btn">Explore</button>
                        <button class="theme-btn package-explore-btn remove-btn">Remove</button>
                    </div>
            </div>
        </section>

    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const wishlistButton = document.querySelector("li:nth-child(5) a");
        const wishlistSidebar = document.getElementById("wishlist-sidebar");
        const closeWishlist = document.getElementById("close-wishlist");

        if (wishlistButton && wishlistSidebar && closeWishlist) {
            wishlistButton.addEventListener("click", function(event) {
                event.preventDefault();
                wishlistSidebar.classList.add("active-sidebar");
            });

            closeWishlist.addEventListener("click", function() {
                wishlistSidebar.classList.remove("active-sidebar");
            });
        }
    });
</script>
</body>
</html>