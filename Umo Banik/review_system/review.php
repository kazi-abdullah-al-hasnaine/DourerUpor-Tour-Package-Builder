
<!DOCTYPE html>
<html>
<head>
    <title>Package Reviews</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f4f4f4; }
        .package { background: #fff; padding: 15px; margin-bottom: 25px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .review { margin-left: 20px; margin-bottom: 10px; }
        .review-form { margin-top: 15px; }
        textarea, input[type="number"] { width: 100%; padding: 8px; margin-top: 5px; }
        input[type="submit"] { margin-top: 10px; padding: 8px 15px; background: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>

<h1>All Packages & Reviews</h1>

<?php foreach ($packages as $pkg): ?>
    <div class="package">
        <h2><?= htmlspecialchars($pkg['package_name']) ?></h2>
        <p><strong>Details:</strong> <?= htmlspecialchars($pkg['details']) ?></p>
        <p><strong>Published:</strong> <?= htmlspecialchars($pkg['publish_time']) ?></p>

        <h4>Reviews:</h4>
        <?php
            $reviewCollection = $reviewManager->getReviewsByPackage($pkg['package_id']);
            $iterator = $reviewCollection->createIterator();
            if (!$iterator->hasNext()) {
                echo "<p class='review'>No reviews yet!</p>";
            }
            while ($iterator->hasNext()):
                $review = $iterator->next();
        ?>
            <div class="review">
                <strong><?= htmlspecialchars($review->user_name) ?></strong> 
                (Rating: <?= $review->rating ?>/5): <br>
                <?= htmlspecialchars($review->review) ?>
            </div>
        <?php endwhile; ?>

        <?php if (!$reviewManager->hasUserReviewed($current_user_id, $pkg['package_id'])): ?>
            <div class="review-form">
                <form method="POST">
                    <input type="hidden" name="package_id" value="<?= $pkg['package_id'] ?>">
                    <label>Rating (1-5):</label>
                    <input type="number" name="rating" min="1" max="5" required>
                    <label>Review:</label>
                    <textarea name="review" required></textarea>
                    <input type="submit" value="Submit Review">
                </form>
            </div>
        <?php else: ?>
            <p><em>You already reviewed this package.</em></p>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

</body>
</html>



<?php
// Database Singleton
class Database {
    private static $instance = null;
    private $conn;

    private $host = "localhost";
    private $db   = "dourerupor";
    private $user = "root";
    private $pass = "";

    private function __construct() {
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->db", $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}

// Review Entity
class Review {
    public function __construct(
        public int $review_id,
        public int $package_id,
        public int $rating,
        public string $review,
        public string $user_name
    ) {}
}

// Iterator Interface
interface ReviewIteratorInterface {
    public function hasNext(): bool;
    public function next(): ?Review;
}

// Collection
class ReviewCollection {
    private array $reviews = [];

    public function addReview(Review $review) {
        $this->reviews[] = $review;
    }

    public function createIterator(): ReviewIteratorInterface {
        return new ReviewIterator($this->reviews);
    }
}

// Iterator
class ReviewIterator implements ReviewIteratorInterface {
    private int $position = 0;

    public function __construct(private array $reviews) {}

    public function hasNext(): bool {
        return $this->position < count($this->reviews);
    }

    public function next(): ?Review {
        return $this->hasNext() ? $this->reviews[$this->position++] : null;
    }
}

// ReviewManager
class ReviewManager {
    private PDO $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getReviewsByPackage(int $package_id): ReviewCollection {
        $stmt = $this->conn->prepare("
            SELECT r.*, u.name as user_name
            FROM reviews r
            JOIN user u ON r.user_id = u.id
            WHERE r.package_id = 13
        ");
        $stmt->execute([$package_id]);

        $collection = new ReviewCollection();
        while ($row = $stmt->fetch()) {
            $review = new Review(
                $row['review_id'],
                $row['package_id'],
                $row['rating'],
                $row['review'],
                $row['user_name']
            );
            $collection->addReview($review);
        }

        return $collection;
    }

    public function hasUserReviewed(int $user_id, int $package_id): bool {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM reviews WHERE user_id = ? AND package_id = ?");
        $stmt->execute([$user_id, $package_id]);
        return $stmt->fetchColumn() > 0;
    }

    public function submitReview(int $package_id, int $rating, string $review, int $user_id) {
        $stmt = $this->conn->prepare("INSERT INTO reviews (package_id, rating, review, user_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$package_id, $rating, $review, $user_id]);
    }
}

// --- Handle Review Form Submission ---
$reviewManager = new ReviewManager();
$current_user_id = 2; // Simulate logged-in user (Tahshan)

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['package_id'])) {
    $package_id = $_POST['package_id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];
    $reviewManager->submitReview($package_id, $rating, $review, $current_user_id);
    header("Location: " . $_SERVER['PHP_SELF']); // Refresh to prevent resubmit
    exit;
}

// --- Fetch Packages and Render ---
$conn = Database::getInstance()->getConnection();
$packages = $conn->query("SELECT * FROM packages")->fetchAll();
?>
