<?php
session_start();
$_SESSION['current-page'] = 'package';
$active_page = 'package';


$packageId = isset($_GET['id']) ? intval($_GET['id']) : null;


// Database connection
require_once 'db_connection/db.php';
$db = Database::getInstance();
$conn = $db->getConnection();

include "decoration.php";

include "DesignPatterns/imageProxy.php";

$conn = Database::getInstance()->getConnection();

// Getting username from DB for navigation bar
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $stmt = $conn->prepare("SELECT name FROM user WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $username = explode(" ", $user['name'])[0];
}




$email = isset($_SESSION['email']) ? $_SESSION['email'] : null;

// ---Package Details----
$selectData = $conn->prepare("
SELECT 
    p.package_id, 
    p.package_name,
    p.details,
    p.image,
    COUNT(DISTINCT pf.user_id) AS Followers,
    AVG(DISTINCT r.rating) AS Rating,
    COUNT(DISTINCT r.review_id) AS TotalReview,
    SUM(DISTINCT pd.money_saved) as Saved,
    MAX(DISTINCT pd.day_count) as TotalDays,
    MAX(CASE WHEN u.email = '{$email}' THEN u.id ELSE NULL END) AS LoggedInUserId
FROM 
    packages p

LEFT JOIN 
    package_details pd ON p.package_id = pd.package_id
LEFT JOIN 
    reviews r ON p.package_id = r.package_id
LEFT JOIN 
    package_followers pf ON p.package_id = pf.package_id
LEFT JOIN
	user u ON pf.user_id = u.id
GROUP BY 
    p.package_id, p.package_name
HAVING p.package_id = {$packageId}
ORDER BY Followers DESC, Rating DESC
");

$selectData->execute();
$rows = $selectData->fetchAll(PDO::FETCH_ASSOC);



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
                <li class="active-page"><a href="home.php" <?php if ($active_page == "home") {
                                                                echo "class='active'";
                                                            } ?>>Home</a></li>
                <li><a <?php if ($active_page == "popular") {
                            echo "class='active'";
                        } ?> href="popular.php">Popular</a></li>
                <li><a <?php if ($active_page == "explore") {
                            echo "class='active'";
                        } ?> href="">Explore</a></li>
                <li><a <?php if ($active_page == "build&share") {
                            echo "class='active'";
                        } ?> href="">Build & Share</a></li>
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
    <?php
    include "modules/wishlist.php";
    ?>
    <section id="package-details">
        <div class="package-details-container">
            <?php foreach ($rows as $row):
                $package_id = $row['package_id'];
                $package_name = $row['package_name'];
                $details = $row['details'];
                $coverImage = $row['image'];
                $totalDays = $row['TotalDays'];
                $followers = $row['Followers'];
                $rating = number_format($row['Rating'], 1);
                $totalReview = $row['TotalReview'];
                $saved = number_format($row['Saved'], 2);
                $loggedInUserId = $row['LoggedInUserId'];
            ?>
                <div class="package-details" data-package-id="<?php echo $package_id; ?>">
                    <div class="left">
                        <div class="package-cover lazy-bg" <?php echo getLazyBackgroundImage("img/package-cover/{$coverImage}"); ?> style="background-repeat: no-repeat; background-size: cover; border-radius: 10px;"></div>

                    </div>
                    <div class="right">
                        <h3 class="package-name card-item"><?php echo htmlspecialchars($package_name); ?></h3>
                        <div class="card-item">
                            <button disabled class="theme-btn info-btn"><?php echo htmlspecialchars($totalDays); ?> day/s</button>
                            <button disabled class="theme-btn info-btn"><?php echo htmlspecialchars($rating); ?>⭐</button>
                            <button disabled class="theme-btn info-btn"><?php echo htmlspecialchars($totalReview); ?>💬</button>
                            <button disabled class="theme-btn info-btn offer">Save ৳<?php echo $saved; ?></button>
                        </div>
                        <p class="card-item package-brief"><?php echo htmlspecialchars($details); ?></p>
                        <div class="card-item">
                            <?php if (isset($_SESSION['email'])): ?>
                                <?php if ($loggedInUserId == null): ?>
                                    <button class="theme-btn follow-btn">Follow</button>
                                <?php else: ?>
                                    <button class="theme-btn remove-btn">Following</button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </section>


    <?php
    // --- Package steps ---
    $selectPackageDetails = $conn->prepare("
    	SELECT p.package_id,pd.step_number as stepCount ,pd.day_count as dayCount, d.cost as DestCost, d.name as Destination, d.type as DestType, dp.name as pickup, pd.transport_type as TransType, pd.cost as transCost, pd.money_saved as Saved
		FROM package_details pd 
		LEFT JOIN packages p ON p.package_id = pd.package_id
		LEFT JOIN destinations d ON d.destination_id = pd.destination_id
		LEFT JOIN destinations dp ON dp.destination_id = pd.pickup
		WHERE p.package_id = {$packageId}
		ORDER BY pd.step_number ASC

    	");
    $selectPackageDetails->execute();
    $rows = $selectPackageDetails->fetchAll(PDO::FETCH_ASSOC);
    ?>




    <section id="package-map">
        <h1 class="package-map-title">Package Map</h1>
        <div class="map-card-container">
            <?php foreach ($rows as $row):
                $packageId   = $row['package_id'];
                $stepCount   = $row['stepCount'];
                $dayCount    = $row['dayCount'];
                $destCost    = $row['DestCost'];
                $destination = $row['Destination'];
                $destType    = $row['DestType'];
                $pickup      = $row['pickup'];
                $transType   = $row['TransType'];
                $transCost   = $row['transCost'];
                $saved       = $row['Saved']
            ?>
                <div class="map-card">
                    <p class="p-day map-item"><?php echo htmlspecialchars($stepCount); ?></p>
                    <p class="p-day map-item">DAY: <?php echo htmlspecialchars($dayCount); ?></p>
                    <p class="p-day p-cost map-item"><i class="fa-solid fa-coins"></i> <?php echo htmlspecialchars("৳ " . $destCost + $transCost - $saved); ?></p>
                    <p class="p-main map-item"><i class="fa-solid fa-location-dot"></i> <b><?php echo htmlspecialchars($destination); ?></b> | <b><?php echo htmlspecialchars($destType); ?></b> | <b>৳<?php echo htmlspecialchars($destCost); ?></b></p>
                    <p class="p-optional map-item">
                        <?php
                        // Set transport icon based on transType
                        $icon = '<i class="fa-solid fa-car"></i>'; // default
                        if (!empty($transType)) {
                            $type = strtolower($transType);
                            if ($type === 'bike' || $type === 'motorcycle') {
                                $icon = '<i class="fa-solid fa-motorcycle"></i>';
                            } elseif ($type === 'train' || $type === 'metro' || $type === 'tram') {
                                $icon = '<i class="fa-solid fa-train"></i>';
                            } elseif ($type === 'plane' || $type === 'airplane' || $type === 'flight') {
                                $icon = '<i class="fa-solid fa-plane"></i>';
                            } elseif ($type === 'bus' || $type === 'public transport' || $type === 'local bus') {
                                $icon = '<i class="fa-solid fa-bus-simple"></i>';
                            }
                        }
                        ?>

                        <?php if (!empty($pickup)): ?>
                            <?php echo $icon . ' ' . htmlspecialchars($pickup . " to "); ?>
                        <?php endif; ?>

                        <?php if (!empty($pickup)): ?>
                            <?php echo htmlspecialchars($destination . " using "); ?>
                        <?php endif; ?>

                        <?php if (!empty($transType)): ?>
                            <?php echo htmlspecialchars($transType . " for ৳" . $transCost . " | "); ?>
                        <?php endif; ?>

                        <i class="fa-solid fa-tag"></i> Saved: ৳<?php echo htmlspecialchars($saved); ?>
                    </p>


                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <!-- Review Section  -->
    <section id="reviews-section">
        <?php $packageIdForReview = $packageId;
        include('modules/review.php')
        ?>
    </section>
    <script src="https://kit.fontawesome.com/1621a0cc57.js" crossorigin="anonymous"></script>
    <!-- // Follow unfollow button functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Attach event listener to all follow/unfollow buttons
            const followButtons = document.querySelectorAll('.follow-btn, .remove-btn');

            followButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const packageId = this.closest('.package-details').dataset.packageId;
                    const isFollow = this.classList.contains('follow-btn');
                    const action = isFollow ? 'follow' : 'unfollow';

                    const data = new FormData();
                    data.append('action', action);
                    data.append('package_id', packageId);

                    // Send the AJAX request
                    fetch('modules/backend/follow-action.php', {
                            method: 'POST',
                            body: data,
                            credentials: 'same-origin'
                        })
                        .then(response => response.json())
                        .then(responseData => {
                            if (responseData.status === 'success') {
                                // Toggle button state based on the action
                                if (isFollow) {
                                    this.textContent = 'Following';
                                    this.classList.remove('follow-btn');
                                    this.classList.add('remove-btn');
                                } else {
                                    this.textContent = 'Follow';
                                    this.classList.remove('remove-btn');
                                    this.classList.add('follow-btn');
                                }
                            } else {
                                console.error('Error:', responseData.message);
                            }
                        })
                        .catch(error => console.error('Request failed', error));
                });
            });
        });
    </script>

</body>

</html>