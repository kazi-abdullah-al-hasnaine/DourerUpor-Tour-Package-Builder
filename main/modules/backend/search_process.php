<?php
session_start();

header('Content-Type: application/json');

// Turn off direct error display 
ini_set('display_errors', 0);

try {
    // Include database connection file
    require_once('../../db_connection/db.php');
    
    if (!isset($_POST['query']) || empty($_POST['query'])) {
        echo json_encode([]);
        exit;
    }
    
    $searchQuery = $_POST['query'];
    
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Search in packages by name
    $stmt = $conn->prepare("
        SELECT 
            p.package_id, 
            p.package_name AS name, 
            'package' AS type, 
            p.details AS description, 
            p.image
        FROM 
            packages p 
        WHERE 
            p.package_name LIKE :query
        
        UNION
        
        -- Search packages by destination
        SELECT 
            p.package_id, 
            p.package_name AS name, 
            'package_by_destination' AS type, 
            p.details AS description, 
            p.image
        FROM 
            packages p
        JOIN 
            package_details pd ON p.package_id = pd.package_id
        JOIN 
            destinations d ON pd.destination_id = d.destination_id
        WHERE 
            d.name LIKE :query
            
        LIMIT 10
    ");
    
    $searchParam = '%' . $searchQuery . '%';
    $stmt->bindParam(':query', $searchParam);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process the results to make them unique by package_id and add destination info
    $uniqueResults = [];
    
    foreach ($results as $result) {
        $package_id = $result['package_id'];
        
        // If we haven't added this package yet
        if (!isset($uniqueResults[$package_id])) {
            try {
                // Get destinations for this package
                $destStmt = $conn->prepare("
                    SELECT 
                        d.name 
                    FROM 
                        package_details pd 
                    JOIN 
                        destinations d ON pd.destination_id = d.destination_id 
                    WHERE 
                        pd.package_id = :package_id
                    ORDER BY 
                        pd.step_number
                    LIMIT 3
                ");
                $destStmt->bindParam(':package_id', $package_id);
                $destStmt->execute();
                $destinations = $destStmt->fetchAll(PDO::FETCH_COLUMN);
                
                // Get reviews count and average rating
                $reviewStmt = $conn->prepare("
                    SELECT 
                        COUNT(*) as review_count,
                        AVG(rating) as avg_rating
                    FROM 
                        reviews
                    WHERE 
                        package_id = :package_id
                ");
                $reviewStmt->bindParam(':package_id', $package_id);
                $reviewStmt->execute();
                $reviewData = $reviewStmt->fetch(PDO::FETCH_ASSOC);
                
                // Format the result
                $result['destinations'] = !empty($destinations) ? implode(' > ', $destinations) : 'No destinations';
                $result['review_count'] = (int)$reviewData['review_count'];
                $result['avg_rating'] = $reviewData['avg_rating'] ? round((float)$reviewData['avg_rating'], 1) : 0;
                
                // Truncate description to 150 characters
                if ($result['description'] && strlen($result['description']) > 150) {
                    $result['description'] = substr($result['description'], 0, 150) . '...';
                } else if (empty($result['description'])) {
                    $result['description'] = 'No description available';
                }
                
                // Store the result
                $uniqueResults[$package_id] = $result;
            } catch (PDOException $innerEx) {
                // Log inner exception but continue with other results
                error_log("Error processing package ID {$package_id}: " . $innerEx->getMessage());
                continue;
            }
        }
    }
    
    echo json_encode(array_values($uniqueResults));
    
} catch (Throwable $e) {
    // Log error to server log
    error_log('Search process error: ' . $e->getMessage());
    
    // Return error as JSON - using array format for consistency
    echo json_encode(['error' => 'Database error occurred. Please try again later.']);
}
?>