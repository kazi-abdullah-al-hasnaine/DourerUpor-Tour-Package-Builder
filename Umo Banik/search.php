<?php
session_start();
$_SESSION['current-page'] = 'search';

// Database connection - use proper relative path
require_once './db_connection/db.php';

// Check if search was submitted
if(isset($_GET['search-box']) && !empty($_GET['search-box'])) {
    $searchQuery = $_GET['search-box'];
    $_SESSION['searchQuery'] = $searchQuery;
} else if(isset($_SESSION['searchQuery'])) {
    $searchQuery = $_SESSION['searchQuery'];
} else {
    // Redirect to home if no search query
    header("Location: home.php");
    exit();
}

include "./decoration.php";
include "./DesignPatterns/pageDecorator.php";
include "./DesignPatterns/imageProxy.php";

// Create base page with search results title
$searchPage = new BasePage("Search Results for \"" . htmlspecialchars($searchQuery) . "\"");

// Create a custom SearchResultsSection class that implements PageComponent
class SearchResultsSection implements PageComponent {
    private $page;
    private $searchQuery;
    
    public function __construct(PageComponent $page, $searchQuery) {
        $this->page = $page;
        $this->searchQuery = $searchQuery;
    }
    
    public function render() {
        $this->page->render();
        
        // Get database connection
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        echo '<section id="popular-section">';
        echo '<div class="title-container">';
        echo '<h1>Search Results for "' . htmlspecialchars($this->searchQuery) . '"</h1>';
        echo '</div>';
        
        // Search query
        $query = "SELECT 
                    p.package_id, p.package_name, p.details, p.image,
                    COUNT(DISTINCT pf.user_id) AS Followers,
                    AVG(DISTINCT r.rating) AS Rating,
                    COUNT(DISTINCT r.review_id) AS TotalReview,
                    SUM(DISTINCT pd.money_saved) as Saved,
                    MAX(DISTINCT pd.day_count) as TotalDays
                  FROM 
                    packages p
                  LEFT JOIN 
                    package_details pd ON p.package_id = pd.package_id
                  LEFT JOIN 
                    reviews r ON p.package_id = r.package_id
                  LEFT JOIN 
                    package_followers pf ON p.package_id = pf.package_id
                  WHERE 
                    p.package_name LIKE :search 
                  GROUP BY 
                    p.package_id, p.package_name
                  ORDER BY Followers DESC, Rating DESC";
        
        $stmt = $conn->prepare($query);
        $searchParam = "%" . $this->searchQuery . "%";
        $stmt->bindParam(':search', $searchParam);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if(count($results) > 0) {
            echo '<div class="package-details-container">';
            foreach($results as $package) {
                $packageId = $package['package_id'];
                $packageName = $package['package_name'];
                $details = $package['details'];
                $coverImage = $package['image'];
                $totalDays = $package['TotalDays'] ?: 0;
                $followers = $package['Followers'] ?: 0;
                $rating = number_format($package['Rating'] ?: 0, 1);
                $totalReview = $package['TotalReview'] ?: 0;
                $saved = number_format($package['Saved'] ?: 0, 2);
                
                echo '<div class="package-card">';
                echo '<div class="left">';
                echo '<div class="package-cover lazy-bg" ' . getLazyBackgroundImage("img/package-cover/{$coverImage}") . ' style="background-repeat: no-repeat; background-size: cover; border-radius: 10px; width: 300px; height: 200px;"></div>';
                echo '</div>';
                echo '<div class="right">';
                echo '<h3 class="package-name card-item">' . htmlspecialchars($packageName) . '</h3>';
                echo '<div class="card-item">';
                echo '<button disabled class="theme-btn info-btn">' . htmlspecialchars($totalDays) . ' day/s</button>';
                echo '<button disabled class="theme-btn info-btn">' . htmlspecialchars($rating) . '⭐</button>';
                echo '<button disabled class="theme-btn info-btn">' . htmlspecialchars($totalReview) . '💬</button>';
                echo '<button disabled class="theme-btn info-btn offer">Save ৳' . $saved . '</button>';
                echo '</div>';
                echo '<p class="card-item package-brief">' . htmlspecialchars(substr($details, 0, 250)) . '...</p>';
                echo '<div class="card-item">';
                echo '<a href="package.php?id=' . $packageId . '"><button class="theme-btn package-explore-btn">Explore</button></a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<div class="no-results" style="text-align: center; margin: 30px 0;">';
            echo '<p>No packages found matching your search. Try different keywords or explore our popular destinations.</p>';
            echo '<a href="popular.php"><button class="theme-btn package-explore-btn">View Popular Destinations</button></a>';
            echo '</div>';
        }
        
        echo '</section>';
    }
}

// Wrap the page with decorators
$decoratedSearchPage = new FooterDecorator(
    new SearchResultsSection($searchPage, $searchQuery)
);

// Render the page
$decoratedSearchPage->render();
?>