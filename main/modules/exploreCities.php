<?php
if ($type == "section") {
    $sectionType = "small-section";
    $cardType = "small-card";
} elseif ($type == "main-content") {
    $cardType = "large-card";
    $sectionType = "large-section";
}

// require_once('db_connection\db.php');

$db = Database::getInstance();
$conn = $db->getConnection();



// SQL query
$query = "SELECT d.name, d.country, d.type, AVG(r.rating) AS avg_rating, p.image
          FROM destinations d
          LEFT JOIN package_details pd ON pd.destination_id = d.destination_id
          LEFT JOIN packages p ON p.package_id = pd.package_id
          LEFT JOIN reviews r ON r.package_id = p.package_id
          GROUP BY d.name
          HAVING d.type = 'City'
          ORDER BY avg_rating DESC
          LIMIT $limit
          ";

$stmt = $conn->prepare($query);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section id="explore-section" class="<?php echo $sectionType; ?>">
    <div class="explore-package-card-container">
    <?php if (!empty($results)): ?>
        <?php foreach ($results as $row): ?>
            <?php
                $cityName = $row['name'];
                $country = $row['country'];
                $rating = number_format($row['avg_rating'], 1);
                $image = $row['image'] ?? 'default.jpg';
            ?>
            <div class="<?php echo $cardType; ?> exlpore-card lazy-bg" 
                 data-bg="<?php echo getLazyBackgroundImage("img/package-cover/{$image}"); ?>" 
                 style="background-repeat: no-repeat; background-size: cover; border-radius: 10px;">
                <div class="explore-wrapper">
                    <h3><?php echo $cityName; ?></h3>
                    <p><?php echo $rating; ?>⭐ • <?php echo $country; ?></p>
                    <div>
                        <button class="theme-btn explore-city-btn">Explore city</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No cities found.</p>
    <?php endif; ?>
</div>
</section>
