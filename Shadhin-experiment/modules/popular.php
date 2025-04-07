<?php 

// fetch the popular packages from the database
$db = Database::getInstance();
$conn = $db->getConnection();

if($_SESSION['current-page'] == 'home'){
    $limit = 2;
}
else{
    $limit = 12;
}

$email = isset($_SESSION['email']) ? $_SESSION['email'] : null;


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
ORDER BY Followers DESC, Rating DESC
LIMIT {$limit}
");

$selectData->execute();
$rows = $selectData->fetchAll(PDO::FETCH_ASSOC);
?>

<section id="popular-section">
        <div class="title-container"><h1>Popular this season</h1></div>
        <div class="popular-content-container">
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
            <div class="package-card" data-package-id="<?php echo $package_id; ?>">
                <div class="left">
                    <div class="package-cover lazy-bg" <?php echo getLazyBackgroundImage("img/package-cover/{$coverImage}"); ?> style="width: 250px; height: 250px; background-repeat: no-repeat; background-size: cover; border-radius: 10px;"></div>

                </div>
                <div class="right">
                    <h3 class="package-name card-item"><?php echo htmlspecialchars($package_name); ?></h3>
                    <div class="card-item">
                        <button disabled class="theme-btn info-btn"><?php echo htmlspecialchars($totalDays); ?> day/s</button>
                        <button disabled class="theme-btn info-btn"><?php echo htmlspecialchars($rating); ?>⭐</button>
                        <button disabled class="theme-btn info-btn"><?php echo htmlspecialchars($totalReview); ?>💬</button>
                        <button disabled class="theme-btn info-btn offer">Save $<?php echo $saved; ?></button>
                    </div>
                    <p class="card-item package-brief"><?php echo $details; ?></p>
                    <div class="card-item">
                        <button class="theme-btn package-explore-btn">Explore</button>
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

    <!-- // Follow unfollow button functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        // Attach event listener to all follow/unfollow buttons
        const followButtons = document.querySelectorAll('.follow-btn, .remove-btn');
        
        followButtons.forEach(button => {
            button.addEventListener('click', function() {
                const packageId = this.closest('.package-card').dataset.packageId;
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

