<?php 
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use your database connection
require_once('C:\xampp2\htdocs\website\DourerUpor-Tour-Package-Builder\Umo Banik\db_connection\db.php');
$conn = Database::getInstance()->getConnection();

if (!isset($packageIdForReview)) {
    echo "<p>Package ID is missing.</p>";
    exit;
}
$packageId = $packageIdForReview; 

$reviewsQuery = $conn->prepare("SELECT r.review_id, r.user_id, r.rating, r.review, u.name FROM reviews r LEFT JOIN user u ON r.user_id = u.id WHERE r.package_id = :packageId");
$reviewsQuery->execute(['packageId' => $packageId]);

$reviews = $reviewsQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<section id="reviews">
    <h3>Reviews</h3>
    <div class="reviews-container">
        <?php if ($reviews): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-item">
                    <h4><?php echo htmlspecialchars($review['name']); ?></h4>
                    <p>Rating: <?php echo htmlspecialchars($review['rating']); ?>/5</p>
                    <p><?php echo nl2br(htmlspecialchars($review['review'])); ?></p>
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
            <input type="hidden" name="packageId" value="<?php echo htmlspecialchars($packageId); ?>">
        </form>
    <?php else: ?>
        <p>You need to <a href="login.php">login</a> to write a review.</p>
    <?php endif; ?>
</section>
