<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use your database connection
require_once('C:\xampp2\htdocs\website\DourerUpor-Tour-Package-Builder\Umo Banik\db_connection\db.php');
$conn = Database::getInstance()->getConnection();

// Define Review and ReviewCollection classes - Iterator design pattern
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Package Reviews</title>
    <!-- CSS File Link -->
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <section id="review" class="review">
    <!-- WRITE REVIEW -->
    <section id="write-review">
        <div class="write-review-container">
            <h2>Review</h2>
            
            <?php
            if (isset($_SESSION['email'])):
                echo "
                <div class='review-form'>
                    <form action='submit_review.php' method='POST'>
                        <div class='form-group'>
                            <label>Rating:</label>
                            <select class='form-control' name='rating' required>
                                <option value='1'>1 ★</option>
                                <option value='2'>2 ★★</option>
                                <option value='3'>3 ★★★</option>
                                <option value='4'>4 ★★★★</option>
                                <option value='5'>5 ★★★★★</option>
                            </select>
                        </div>
                        <div class='form-group'>
                            <br><label>Drop a Review</label><br>
                            <textarea class='form-control' name='comment' placeholder='Write your review here...' required></textarea>

                        </div>
                        <input type='hidden' name='packageId' value='{$packageId}'>
                        <button type='submit' class='btn btn-custom'>Submit Review</button>
                    </form>
                </div>";
            else:
                echo "
                <div class='review-form'>
                    <p>You need to <a href='login.php'>login</a> to write a review.</p>
                </div>";
            endif;
            ?>
        </div>
    </section> 
    <!-- DISPLAYING ALL REVIEWS -->
    <section id="all-reviews">
        <div class="reviews-list">
            <h3>All Reviews</h3>
            
            <div class="reviews-container">
                <?php
                if ($reviewCollection->valid()):
                    foreach ($reviewCollection as $review):
                        echo "
                        <div class='review-item'>
                            <h4>{$review->userName}</h4>
                            <p>Rating: 
                            <span class='stars'>";
                            for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $review->rating) {
                                echo "<span class='filled'>&#9733;</span>"; 
                            } else {
                                echo "&#9733;"; 
                                }
                            }
                        echo "</span>
                        </p>";
                        echo"
                        <p>{$review->comment}</p>
                        </div>";
                    endforeach;
                else:
                    echo "
                    <div class='review-item'>
                        <p>No reviews yet.</p>
                    </div>
                    ";
                endif;
                ?>
            </div>
        </div>
    </section>
    </section>

</body>
</html>