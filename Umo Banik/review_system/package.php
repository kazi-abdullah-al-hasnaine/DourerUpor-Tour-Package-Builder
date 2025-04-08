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

// Getting username from DB for navigation bar
if(isset($_SESSION['email'])){
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
$packageRows = $selectData->fetchAll(PDO::FETCH_ASSOC);

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $package_id = $_POST['package_id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];

    $stmt = $conn->prepare("SELECT * FROM reviews WHERE package_id = ? AND user_id = ?");
    $stmt->execute([$package_id, $user_id]);
    if ($stmt->rowCount() == 0) {
        $stmt = $conn->prepare("INSERT INTO reviews (package_id, rating, review, user_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$package_id, $rating, $review, $user_id]);
        echo "<p style='color:green;'>Review submitted successfully!</p>";
    } else {
        echo "<p style='color:red;'>You already reviewed this package.</p>";
    }
}

class ReviewIterator implements Iterator {
    private array $reviews;
    private int $position = 0;

    public function __construct(array $reviews) {
        $this->reviews = $reviews;
    }

    public function current(): mixed {
        return $this->reviews[$this->position];
    }

    public function key(): int {
        return $this->position;
    }

    public function next(): void {
        ++$this->position;
    }

    public function rewind(): void {
        $this->position = 0;
    }

    public function valid(): bool {
        return isset($this->reviews[$this->position]);
    }
}

class ReviewCollection {
    private $reviews = [];

    public function __construct($conn, $package_id) {
        $stmt = $conn->prepare("SELECT * FROM reviews WHERE package_id = ? ORDER BY review_id ASC");
        $stmt->execute([$package_id]);
        $this->reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getIterator() {
        return new ReviewIterator($this->reviews);
    }
}

function getUserName($conn, $user_id) {
    $stmt = $conn->prepare("SELECT name FROM user WHERE id = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['name'] : 'Unknown';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Package Details & Reviews</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: radial-gradient(circle, rgba(232,247,244,1) 0%, rgba(211,231,228,1) 100%);
            font-family: Arial, sans-serif;
            color: #333;
        }
    </style>
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

<section id="package-details">
    <div class="package-details-container">
        <?php foreach ($packageRows as $row): 
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

<section id="package-reviews">
    <h1 class="container mt-5">All Reviews</h1>
    <?php
    $reviewCollection = new ReviewCollection($conn, $packageId);
    $iterator = $reviewCollection->getIterator();
    $hasReviews = false;

    foreach ($iterator as $review) {
        $hasReviews = true;
        $userName = getUserName($conn, $review['user_id']);
        echo "<div class='review mb-2'><strong>$userName</strong> (Rating: {$review['rating']}/5):<br>" . htmlspecialchars($review['review']) . "</div>";
    }

    if (!$hasReviews) {
        echo "<p>No reviews yet!</p>";
    }

    $user_id = $_SESSION['user_id'];
    $check = $conn->prepare("SELECT * FROM reviews WHERE package_id = ? AND user_id = ?");
    $check->execute([$packageId, $user_id]);
    if ($check->rowCount() === 0): ?>
        <div class="review-form mt-4">
            <form method="POST">
                <input type="hidden" name="package_id" value="<?= $packageId ?>">
                <label>Rating (1-5):</label>
                <input type="number" name="rating" min="1" max="5" required>
                <label>Review:</label>
                <textarea name="review" required></textarea>
                <input type="submit" value="Submit Review" class="btn btn-primary mt-2">
            </form>
        </div>
    <?php else: ?>
        <p><em>You already reviewed this package.</em></p>
    <?php endif; ?>
</section>

</body>
</html>
