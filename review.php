<?php   
include('C:\xampp2\htdocs\website\DourerUpor-Tour-Package-Builder\Umo Banik\db_connection\db.php');

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
        $this->position = 0;
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

function getReviewsForPackage($conn, $packageId) {
    $stmt = $conn->prepare("
        SELECT user.name AS userName, reviews.rating, reviews.review
        FROM reviews
        JOIN user ON reviews.user_id = user.id
        WHERE reviews.package_id = ?
    ");
    $stmt->execute([$packageId]);

    $reviews = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $reviews[] = new Review($row['userName'], $row['rating'], $row['review']);
    }

    return new ReviewCollection($reviews);
}

$db = Database::getInstance();
$conn = $db->getConnection();

$packageId = 1;
$reviewCollection = getReviewsForPackage($conn, $packageId);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Reviews</title>
    <link rel="stylesheet" href="reviewstyles.css"> <!-- Link to your CSS file -->
</head>
<body>

<div class="container">
    <h1>Package Reviews</h1>

    <?php if ($reviewCollection->valid()): ?>
        <div class="reviews">
            <?php foreach ($reviewCollection as $review): ?>
                <div class="review-card">
                    <h4 class="review-username"><?= htmlspecialchars($review->userName) ?></h4>
                    <div class="review-rating">
                        <?= str_repeat("★", $review->rating) ?>
                    </div>
                    <p class="review-comment"><?= htmlspecialchars($review->comment) ?></p>
                </div>
                <hr>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No reviews available for this package.</p>
    <?php endif; ?>
</div>

</body>
</html>
