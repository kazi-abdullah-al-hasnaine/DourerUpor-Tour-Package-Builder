<?php


$active_page = $_SESSION['current-page'];

// Getting username from DB for navigation bar
if (isset($_SESSION['email'])) {
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
                        <li class="active-page"><a href="home.php" <?php if ($active_page == "home") {
                                                                        echo "class='active'";
                                                                    } ?>>Home</a></li>
                        <li><a <?php if ($active_page == "popular") {
                                    echo "class='active'";
                                } ?> href="popular.php">Popular</a></li>
                        <li><a <?php if ($active_page == "explore") {
                                    echo "class='active'";
                                } ?> href="explore.php">Explore</a></li>
                        <li><a <?php if ($active_page == "buildAndShare") {
                                    echo "class='active'";
                                } ?> href="buildAndShare.php">Build & Share</a></li>
                        <li><a href="">Wishlist</a></li>
                    </ul>
                </div>
                <div>
                    <?php if (isset($_SESSION['email'])): ?>
                        <form action="./Login/user-actions.php" method="post">
                            <button type="Submit" class="theme-btn log-out-btn" title="Click to logout" name="log-out-btn"><?php echo $username; ?></button>
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
                    <form action="search.php" method="GET">
                        <input placeholder="Search your dream destination..." type="text" id="search-box" name="search-box">
                        <button type="submit" class="search-btn">🔍</button>
                    </form>
                </div>
            </div>
        </div>
    </section>


    <!-- ----For wishlist sidebar ----- -->

<?php 
    include "wishlist.php";
?>
<script>
(function(){if(!window.chatbase||window.chatbase("getState")!=="initialized"){window.chatbase=(...arguments)=>{if(!window.chatbase.q){window.chatbase.q=[]}window.chatbase.q.push(arguments)};window.chatbase=new Proxy(window.chatbase,{get(target,prop){if(prop==="q"){return target.q}return(...args)=>target(prop,...args)}})}const onLoad=function(){const script=document.createElement("script");script.src="https://www.chatbase.co/embed.min.js";script.id="YrrEV-HmhhBJr_IDWk8u-";script.domain="www.chatbase.co";document.body.appendChild(script)};if(document.readyState==="complete"){onLoad()}else{window.addEventListener("load",onLoad)}})();
</script>
</body>

</html>