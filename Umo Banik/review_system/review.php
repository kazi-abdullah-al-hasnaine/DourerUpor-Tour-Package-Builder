<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use your database connection
require_once('C:\xampp2\htdocs\website\DourerUpor-Tour-Package-Builder\Umo Banik\db_connection\db.php');
$conn = Database::getInstance()->getConnection();

// Define Review and ReviewCollection classes
class Review {
    public $userName;
    public $rating;
    public $comment;

    public function __construct($userName, $rating, $comment) {
        $this->userName = $userName;
        $this->rating = $rating;
        $this->comment = $comment;
    }
}

class ReviewCollection implements Iterator {
    private $reviews = [];
    private $position = 0;

    public function __construct($reviews = []) {
        $this->reviews = $reviews;
    }

    public function addReview(Review $review): void {
        $this->reviews[] = $review;
    }

    public function current(): Review {
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

// Fetch reviews using a function
function getReviewsForPackage($conn, $packageId): ReviewCollection {
    $stmt = $conn->prepare("
        SELECT u.name AS userName, r.rating, r.review
        FROM reviews r
        JOIN user u ON r.user_id = u.id
        WHERE r.package_id = ?
    ");
    $stmt->execute([$packageId]);

    $reviews = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $reviews[] = new Review($row['userName'], $row['rating'], $row['review']);
    }

    return new ReviewCollection($reviews);
}

// Check if package ID is set
if (!isset($packageIdForReview)) {
    echo "<p>Package ID is missing.</p>";
    exit;
}

$packageId = $packageIdForReview;
$reviewCollection = getReviewsForPackage($conn, $packageId);
?>

<section id="reviews">
    <h3>Reviews</h3>
    <div class="reviews-container">
        <?php if ($reviewCollection->valid()): ?>
            <?php foreach ($reviewCollection as $review): ?>
                <div class="review-item">
                    <h4><?= htmlspecialchars($review->userName) ?></h4>
                    <p>Rating: <?= htmlspecialchars($review->rating) ?>/5</p>
                    <p><?= nl2br(htmlspecialchars($review->comment)) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No reviews yet.</p>
        <?php endif; ?>
    </div>

    <h3>Write a Review</h3>
    <?php if (isset($_SESSION['email'])): ?>
        <form action="submit_review.php" method="POST">
            <textarea name="comment" placeholder="Write your review here..." required></textarea>
            <select name="rating" required>
                <option value="1">1 Star</option>
                <option value="2">2 Stars</option>
                <option value="3">3 Stars</option>
                <option value="4">4 Stars</option>
                <option value="5">5 Stars</option>
            </select>
            <button type="submit">Submit Review</button>
            <input type="hidden" name="packageId" value="<?= htmlspecialchars($packageId) ?>">
        </form>
    <?php else: ?>
        <p>You need to <a href="login.php">login</a> to write a review.</p>
    <?php endif; ?>
</section>
