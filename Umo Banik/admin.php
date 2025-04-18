<?php
session_start();

if(!isset($_SESSION['admin'])) {
    header('Location: admin-login.php');
    exit();
}

if(isset($_SESSION['email']) || isset($_SESSION['user_id'])) {
    unset($_SESSION['email']);
    unset($_SESSION['user_id']);
}

require_once 'db_connection/db.php';
$db = Database::getInstance();
$conn = $db->getConnection();

include_once('DesignPatterns/approvalState.php');
include_once('DesignPatterns/PackageObserver.php'); 

if(isset($_GET['logout'])) {
    // Clear all session variables
    session_unset();
    // Destroy the session
    session_destroy();
    // Redirect to login page
    header('Location: admin-login.php');
    exit();
}

//from observer design pattern for notification
$packageSubject = PackageSubject::getInstance();

// Handle package state changes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['package_id']) && isset($_POST['action'])) {
        $packageId = $_POST['package_id'];
        $action = $_POST['action'];
        
        $package = new Package($packageId);
        
        // Load followers as observers for this package
        $packageSubject->loadFollowersAsObservers($packageId, $conn);
        
        if ($action === 'approve') {
            if ($package->approve()) {
                // Notifying followers that package has been approved
                $packageInfo = $package->getPackageInfo($packageId);
                $message = "Package '{$packageInfo['package_name']}' has been approved and is now available!";
                $packageSubject->notify($packageId, $message);
            }
        } elseif ($action === 'reject') {
            $feedback = $_POST['rejection_feedback'] ?? 'Package does not meet our requirements.';
            if ($package->reject($feedback)) {
                // Notifying followers about rejection
                $packageInfo = $package->getPackageInfo($packageId);
                $message = "Package '{$packageInfo['package_name']}' has been rejected.";
                $packageSubject->notify($packageId, $message);
            }
        } elseif ($action === 'pending') {
            if ($package->setPending()) {
                // Notifying followers about pending status
                $packageInfo = $package->getPackageInfo($packageId);
                $message = "Package '{$packageInfo['package_name']}' is under review.";
                $packageSubject->notify($packageId, $message);
            }
        }
        
        // Redirect to avoid form resubmission
        header("Location: admin.php" . (isset($_GET['view']) ? "?view=" . $_GET['view'] : ""));
        exit;
    }
}

// Rest of the admin.php code remains the same
// ..

// Determine which view to show
$currentView = $_GET['view'] ?? 'pending';

// Filter settings
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$dateFilter = isset($_GET['date_filter']) ? $_GET['date_filter'] : 'all';

// Base query
$query = "
    SELECT p.package_id, p.package_name, p.publish_time, p.status, u.name as creator_name, p.details, p.image
    FROM packages p
    JOIN user u ON p.build_by = u.id WHERE 1=1
";

// Add status filter
if ($currentView === 'pending') {
    $query .= " AND p.status = 'pending'";
} elseif ($currentView === 'approved') {
    $query .= " AND p.status = 'approved'";
} elseif ($currentView === 'rejected') {
    $query .= " AND p.status = 'rejected'";
}

// Add search filter
if (!empty($searchTerm)) {
    $query .= " AND (p.package_name LIKE :search OR u.name LIKE :search)";
}

// Add date filter
if ($dateFilter === 'today') {
    $query .= " AND DATE(p.publish_time) = CURDATE()";
} elseif ($dateFilter === 'week') {
    $query .= " AND p.publish_time >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
} elseif ($dateFilter === 'month') {
    $query .= " AND p.publish_time >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
}

$query .= " ORDER BY p.publish_time DESC";

$stmt = $conn->prepare($query);

if (!empty($searchTerm)) {
    $searchParam = "%$searchTerm%";
    $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
}

$stmt->execute();
$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);


$pendingPackage = new Package();
$pendingPackage->setState(new PendingState());
$pendingCount = $pendingPackage->getStateCount();

$approvedPackage = new Package();
$approvedPackage->setState(new ApprovedState());
$approvedCount = $approvedPackage->getStateCount();

$rejectedPackage = new Package();
$rejectedPackage->setState(new RejectedState());
$rejectedCount = $rejectedPackage->getStateCount();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Management</title>
    <!-- Bootstrap Link -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="admin.css?v=<?php echo time(); ?>">
    <style>
    </style>
</head>
<body>
    <div class="admin-container">
    <div class="admin-header">
        <div class="header-title">
            <h1><i class="bi bi-person-workspace"></i> Admin Dashboard</h1>
        </div>
        <div class="admin-actions">
            <a href="admin.php?logout=true" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    
    <?php if(isset($_SESSION['admin_message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['admin_message_type']; ?>">
        <?php 
            echo $_SESSION['admin_message']; 
            unset($_SESSION['admin_message']);
            unset($_SESSION['admin_message_type']);
        ?>
    </div>
        <?php endif; ?>
        
        <div class="admin-stats">
            <div class="stat-card">
                <i class="bi bi-clock-history"></i>
                <h3><?php echo $pendingCount; ?></h3>
                <p>Pending Packages</p>
            </div>
            <div class="stat-card">
                <i class="bi bi-check-circle"></i>
                <h3><?php echo $approvedCount; ?></h3>
                <p>Approved Packages</p>
            </div>
            <div class="stat-card">
                <i class="bi bi-x-circle"></i>
                <h3><?php echo $rejectedCount; ?></h3>
                <p>Rejected Packages</p>
            </div>
        </div>
        
        <div class="admin-nav">
            <div class="nav-tabs">
                <a href="?view=pending" class="nav-tab <?php echo $currentView === 'pending' ? 'active' : ''; ?>">
                    <i class="bi bi-hourglass-split"></i> Pending
                </a>
                <a href="?view=approved" class="nav-tab <?php echo $currentView === 'approved' ? 'active' : ''; ?>">
                    <i class="bi bi-check-circle"></i> Approved
                </a>
                <a href="?view=rejected" class="nav-tab <?php echo $currentView === 'rejected' ? 'active' : ''; ?>">
                    <i class="bi bi-x-circle"></i> Rejected
                </a>
            </div>
        </div>
        
        <form method="GET" action="" class="filters">
            <input type="hidden" name="view" value="<?php echo $currentView; ?>">
            <input type="text" name="search" placeholder="Search packages or creators..." class="search-box" value="<?php echo htmlspecialchars($searchTerm); ?>">
            
            <select name="date_filter" class="dropdown">
                <option value="all" <?php echo $dateFilter === 'all' ? 'selected' : ''; ?>>All Time</option>
                <option value="today" <?php echo $dateFilter === 'today' ? 'selected' : ''; ?>>Today</option>
                <option value="week" <?php echo $dateFilter === 'week' ? 'selected' : ''; ?>>This Week</option>
                <option value="month" <?php echo $dateFilter === 'month' ? 'selected' : ''; ?>>This Month</option>
            </select>
            
            <button type="submit" class="btn-view">
                <i class="fas fa-filter"></i> Filter
            </button>
        </form>
        
        <div class="packages-container">
            <h2>
                <?php 
                if ($currentView === 'pending') {
                    echo '<i class="bi bi-hourglass-split"></i> Pending Packages';
                } elseif ($currentView === 'approved') {
                    echo '<i class="bi bi-check-circle"></i> Approved Packages';
                } elseif ($currentView === 'rejected') {
                    echo '<i class="bi bi-x-circle"></i> Rejected Packages';
                }
                ?>
            </h2>
            
            <?php if (count($packages) > 0): ?>
                <?php foreach ($packages as $package): ?>
                    <div class="package-card">
                        <div class="package-header">
                            <h3><?php echo htmlspecialchars($package['package_name']); ?></h3>
                            <span class="package-date">
                                Published: <?php echo date('M d, Y', strtotime($package['publish_time'])); ?>
                                <span class="status-badge status-<?php echo $package['status']; ?>">
                                    <?php echo ucfirst($package['status']); ?>
                                </span>
                            </span>
                        </div>
                        
                        <div class="package-content">
                            <div class="package-image">
                                <img src="img/package-cover/<?php echo $package['image']; ?>" alt="Package Image">
                            </div>
                            
                            <div class="package-info">
                                <p><strong>Created by:</strong> <?php echo htmlspecialchars($package['creator_name']); ?></p>
                                <p class="package-description"><?php echo substr(htmlspecialchars($package['details']), 0, 200) . '...'; ?></p>
                                
                                <div class="package-actions">
                                    <a href="package.php?id=<?php echo $package['package_id']; ?>" class="btn-view" target="_blank">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    
                                    <div class="action-dropdown">
                                        <button class="btn-view">
                                            <i class="fas fa-cog"></i> Change Status
                                        </button>
                                        <div class="dropdown-content">
                                            <?php if ($package['status'] !== 'approved'): ?>
                                                <form method="POST" class="inline-form">
                                                    <input type="hidden" name="package_id" value="<?php echo $package['package_id']; ?>">
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="btn-status-change approve">
                                                        <i class="fas fa-check"></i> Mark as Approved
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($package['status'] !== 'rejected'): ?>
                                                <button type="button" class="btn-status-change reject" 
                                                    onclick="openRejectionModal('<?php echo $package['package_id']; ?>')">
                                                    <i class="fas fa-times"></i> Mark as Rejected
                                                </button>
                                            <?php endif; ?>
                                            
                                            <?php if ($package['status'] !== 'pending'): ?>
                                                <form method="POST" class="inline-form">
                                                    <input type="hidden" name="package_id" value="<?php echo $package['package_id']; ?>">
                                                    <input type="hidden" name="action" value="pending">
                                                    <button type="submit" class="btn-status-change pending">
                                                        <i class="fas fa-hourglass"></i> Mark as Pending
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-packages">
                    <i class="fas fa-info-circle"></i>
                    <p>No <?php echo $currentView; ?> packages found!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div id="rejectionModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2><i class="fas fa-times-circle"></i> Reject Package</h2>
            <form id="rejectionForm" method="POST">
                <input type="hidden" id="rejection_package_id" name="package_id">
                <input type="hidden" name="action" value="reject">
                <div class="form-group">
                    <label for="rejection_feedback">Provide feedback to the package creator:</label>
                    <textarea id="rejection_feedback" name="rejection_feedback" rows="4" 
                        placeholder="Please explain why this package is being rejected..." required></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" id="cancelRejection">Cancel</button>
                    <button type="submit" class="btn-reject">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add destination Form -->
    <div class="dest-container">
        <h2><i class="bi bi-geo-fill"></i> Add New Destination</h2>
        <div class="dest-card">
            <div class="dest-header">
                <h3>Create New Destination</h3>
            </div>
            <div class="dest-content">
                <form id="destination-form" action="admin-actions.php" method="post">
                    <div class="form-group">
                        <label for="destName">Destination Name</label>
                        <input type="text" class="search-box" id="destName" placeholder="Enter new destination" name="dest" required>
                    </div>
                    <div class="form-group">
                        <label for="destType">Destination Type</label>
                        <select id="destType" class="dropdown" name="destType" required>
                            <option value="">Select Destination Type</option>
                            <option value="City">City</option>
                            <option value="Park">Park</option>
                            <option value="Landmark">Landmark</option>
                            <option value="Museum">Museum</option>
                            <option value="Destination">Destination</option>
                            <option value="Hotel">Hotel</option>
                            <option value="Beach">Beach</option>
                            <option value="Mountain">Mountain</option>
                        </select>
                    </div>   
                    <div class="form-group">
                        <label for="destCost">Cost (if applicable)</label>
                        <input type="number" class="search-box" id="destCost" placeholder="Enter cost (0 for free)" name="destCost" value="0" min="0">
                    </div>
                    
                    <button type="submit" class="btn-view" name="dest-button">
                        <i class="fas fa-plus"></i> Add Destination
                    </button>
                </form>
            </div>
        </div>
        
    <script>
        // Modal handling code
        const modal = document.getElementById("rejectionModal");
        const closeBtn = document.getElementsByClassName("close")[0];
        const cancelBtn = document.getElementById("cancelRejection");

        // Function to open the rejection modal
        function openRejectionModal(packageId) {
            document.getElementById('rejection_package_id').value = packageId;
            modal.style.display = "block";
        }

        // Close the modal when clicking on X or Cancel
        closeBtn.onclick = function() {
            modal.style.display = "none";
        }

        cancelBtn.onclick = function() {
            modal.style.display = "none";
        }

        // Close if clicked outside the modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>