<?php
// Start session
session_start();
$_SESSION['current-page'] = 'profile';
$active_page = 'profile';

// Set user ID (using ID 3 as requested)
$userId = 3;

// Database connection
require_once 'db_connection/db.php';
$db = Database::getInstance();
$conn = $db->getConnection();

// Function to get user data
function getUserData($conn, $userId) {
    $query = "SELECT id, name, email, dob, country FROM user WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$userId]);
    
    if ($stmt->rowCount() > 0) {
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        return null;
    }
}

// Function to get packages created by user
function getCreatedPackages($conn, $userId) {
    $query = "SELECT package_id, package_name, publish_time, status FROM packages WHERE build_by = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$userId]);
    
    $packages = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $packages[] = $row;
    }
    
    return $packages;
}

// Function to get packages followed by user
function getFollowedPackages($conn, $userId) {
    $query = "SELECT p.package_id, p.package_name, pf.time 
              FROM package_followers pf 
              JOIN packages p ON pf.package_id = p.package_id 
              WHERE pf.user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$userId]);
    
    $packages = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $packages[] = $row;
    }
    
    return $packages;
}

// Get data from database
$userData = getUserData($conn, $userId);
$createdPackages = getCreatedPackages($conn, $userId);
$followedPackages = getFollowedPackages($conn, $userId);

// Generate user initials
function getInitials($name) {
    $words = explode(' ', $name);
    $initials = '';
    foreach ($words as $word) {
        $initials .= strtoupper(substr($word, 0, 1));
    }
    return $initials;
}

// Format date
function formatDate($dateString) {
    $date = new DateTime($dateString);
    return $date->format('F j, Y');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        
        .profile-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid rgba(255, 255, 255, 0.5);
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e0e0e0;
            font-size: 40px;
            color: #6a11cb;
        }
        
        .profile-name {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .profile-email {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .profile-details {
            padding: 20px;
        }
        
        .detail-item {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            display: flex;
        }
        
        .detail-item:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            width: 120px;
            color: #555;
        }
        
        .detail-value {
            flex: 1;
        }
        
        .error {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        
        .section-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .section-title {
            padding: 15px 20px;
            font-size: 18px;
            font-weight: 600;
            background-color: #f9f9f9;
            border-bottom: 1px solid #eee;
            color: #333;
        }
        
        .section-content {
            padding: 0;
        }
        
        .package-item {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .package-item:last-child {
            border-bottom: none;
        }
        
        .package-info {
            flex: 1;
        }
        
        .package-name {
            font-weight: 600;
            color: #2575fc;
            text-decoration: none;
            font-size: 16px;
        }
        
        .package-name:hover {
            text-decoration: underline;
        }
        
        .package-date, .package-status {
            font-size: 12px;
            color: #777;
            margin-top: 3px;
        }
        
        .package-status.pending {
            color: #f57c00;
        }
        
        .package-status.approved {
            color: #43a047;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2575fc;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #1a5dc8;
        }
        
        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .edit-profile {
            text-align: center;
            padding: 20px;
        }
        
        .no-packages {
            padding: 20px;
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($userData): ?>
        <!-- User Profile Card -->
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-img"><?php echo getInitials($userData['name']); ?></div>
                <h2 class="profile-name"><?php echo htmlspecialchars($userData['name']); ?></h2>
                <p class="profile-email"><?php echo htmlspecialchars($userData['email']); ?></p>
            </div>
            <div class="profile-details">
                <div class="detail-item">
                    <div class="detail-label">Name</div>
                    <div class="detail-value"><?php echo htmlspecialchars($userData['name']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Email</div>
                    <div class="detail-value"><?php echo htmlspecialchars($userData['email']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Country</div>
                    <div class="detail-value"><?php echo htmlspecialchars($userData['country']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Date of Birth</div>
                    <div class="detail-value"><?php echo formatDate($userData['dob']); ?></div>
                </div>
            </div>
            <div class="edit-profile">
                <a href="edit-profile.php?id=<?php echo $userData['id']; ?>" class="btn">Edit Profile</a>
            </div>
        </div>
        
        <!-- Created Packages Section -->
        <div class="section-card">
            <div class="section-title">Packages Created by You</div>
            <div class="section-content">
                <?php if (count($createdPackages) > 0): ?>
                    <?php foreach ($createdPackages as $package): ?>
                        <div class="package-item">
                            <div class="package-info">
                                <a href="package-details.php?id=<?php echo $package['package_id']; ?>" class="package-name">
                                    <?php echo htmlspecialchars($package['package_name']); ?>
                                </a>
                                <div class="package-date">Created on: <?php echo formatDate($package['publish_time']); ?></div>
                                <div class="package-status <?php echo $package['status']; ?>">
                                    Status: <?php echo ucfirst($package['status']); ?>
                                </div>
                            </div>
                            <div>
                                <a href="edit-package.php?id=<?php echo $package['package_id']; ?>" class="btn btn-small">Edit</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-packages">You haven't created any packages yet.</div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Followed Packages Section -->
        <div class="section-card">
            <div class="section-title">Packages You Follow</div>
            <div class="section-content">
                <?php if (count($followedPackages) > 0): ?>
                    <?php foreach ($followedPackages as $package): ?>
                        <div class="package-item">
                            <div class="package-info">
                                <a href="package-details.php?id=<?php echo $package['package_id']; ?>" class="package-name">
                                    <?php echo htmlspecialchars($package['package_name']); ?>
                                </a>
                                <div class="package-date">Followed on: <?php echo formatDate($package['time']); ?></div>
                            </div>
                            <div>
                                <a href="unfollow-package.php?id=<?php echo $package['package_id']; ?>&user_id=<?php echo $userId; ?>" class="btn btn-small">Unfollow</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-packages">You're not following any packages.</div>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
            <div class="error">User not found!</div>
        <?php endif; ?>
    </div>
</body>
</html>