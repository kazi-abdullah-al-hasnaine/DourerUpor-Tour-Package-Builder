<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use your database connection
require_once('.\db_connection\db.php');
$db = Database::getInstance();
$conn = $db->getConnection();

$email = $_SESSION['email'] ?? null;

// Get the logged-in user's ID
$stmt = $conn->prepare("SELECT id FROM user WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

include('.\DesignPatterns\reviewIterator.php');

// Fetch reviews using a function
function getReviewsForPackage($conn, $packageId): ReviewCollection
{
    $stmt = $conn->prepare("
        SELECT  r.review_id AS reviewId, r.user_id AS userID, u.name AS userName, r.rating, r.review
        FROM reviews r
        JOIN user u ON r.user_id = u.id
        WHERE r.package_id = ? ORDER BY r.review_publish_time DESC;
    ");
    $stmt->execute([$packageId]);

    $reviews = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $reviews[] = new Reviews($row['reviewId'], $row['userID'], $row['userName'], $row['rating'], $row['review']);
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
    <!-- Bootstrap Link -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- CSS File Link -->
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <section id="review" class="review">
        <!-- WRITE REVIEW -->
        <section id="write-review">
            <div class="write-review-container">
                <h2>Drop a review</h2>

                <?php
                if (isset($_SESSION['email'])):
                    echo "
                <div class='review-form'>
                    <form action='modules/backend/review-action.php' method='POST'>
                    <div class='form-group'>
                        <label>Rating:</label>
                        <select class='form-control' id='user_rating' name='rating' required>
                            <option value='1'>1 ★</option>
                            <option value='2'>2 ★★</option>
                            <option value='3'>3 ★★★</option>
                            <option value='4'>4 ★★★★</option>
                            <option value='5'>5 ★★★★★</option>
                        </select>
                    </div>
                    <div class='form-group'>
                        <br><label>Drop a Review</label><br>
                        <textarea class='form-control' name='review' id='user_review' placeholder='Write your review here...' required></textarea>
                    </div>
                    <input type='hidden' name='package_id' value='{$packageId}'>
                    <button type='submit' name='submit-review-btn' class='btn btn-custom'>Submit Review</button>
                </form>
                </div>";
                else:
                    echo "
                <div class='review-form'>
                    <p>You need to <a href='login/login.html'>login</a> to write a review.</p>
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
                    $reviewIterator = $reviewCollection->createIterator();
                    if ($reviewIterator->hasNext()):
                        while ($reviewIterator->hasNext()):
                            $reviews = $reviewIterator->next();
                            echo "
                        <div class='review-item'>
                            <h4>{$reviews->userName}</h4>
                            <p>Rating: 
                            <span class='stars'>";
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $reviews->rating) {
                                    echo "<span class='filled'>&#9733;</span>";
                                } else {
                                    echo "&#9733;";
                                }
                            }
                            echo "</span>
                            </p>";
                            echo "
                            <p>{$reviews->review}</p>";

                            if ($user && ($reviews->userID == $user['id'])):
                                echo "
                                <div class='review-actions'>
                                    <button class='review-edit-btn' onclick='showEditForm({$reviews->reviewId}, {$reviews->rating}, `{$reviews->review}`)'>
                                        <i class='bi bi-pencil-square'></i> Edit
                                    </button>
                                    <form action='modules/backend/review-action.php' method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this review?\");'>
                                        <input type='hidden' name='review_id' value='{$reviews->reviewId}'>
                                        <input type='hidden' name='package_id' value='{$packageId}'>
                                        <button type='submit' name='delete-review-btn' class='review-delete-btn'>
                                            <i class='bi bi-trash3-fill'></i> Delete
                                        </button>
                                    </form>
                                </div>
                                
                                <div class='edit-form' id='edit-form-{$reviews->reviewId}'>
                                    <form action='modules/backend/review-action.php' method='POST'>
                                        <div class='form-group'>
                                            <label>Rating:</label>
                                            <select class='form-control' name='rating' id='edit-rating-{$reviews->reviewId}' required>
                                                <option value='1'>1 ★</option>
                                                <option value='2'>2 ★★</option>
                                                <option value='3'>3 ★★★</option>
                                                <option value='4'>4 ★★★★</option>
                                                <option value='5'>5 ★★★★★</option>
                                            </select>
                                        </div>
                                        <div class='form-group'>
                                            <br><label>Edit Your Review</label><br>
                                            <textarea class='form-control' name='review' id='edit-review-{$reviews->reviewId}' required></textarea>
                                        </div>
                                        <input type='hidden' name='review_id' value='{$reviews->reviewId}'>
                                        <input type='hidden' name='package_id' value='{$packageId}'>
                                        <button type='submit' name='update-review-btn' class='save-btn'>Save Changes</button>
                                        <button type='button' class='cancel-btn' onclick='hideEditForm({$reviews->reviewId})'>Cancel</button>
                                    </form>
                                </div>";
                            endif;

                            echo "</div>";
                        endwhile;
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

    <script>
        // Function to show edit form
        function showEditForm(reviewId, rating, comment) {
            // Show the specific edit form
            const editForm = document.getElementById(`edit-form-${reviewId}`);
            if (editForm) {
                editForm.style.display = 'block';

                // Set current values
                document.getElementById(`edit-rating-${reviewId}`).value = rating;
                document.getElementById(`edit-review-${reviewId}`).value = comment;
            }
        }

        // Function to hide edit form
        function hideEditForm(reviewId) {
            const editForm = document.getElementById(`edit-form-${reviewId}`);
            if (editForm) {
                editForm.style.display = 'none';
            }
        }
    </script>
</body>

</html>