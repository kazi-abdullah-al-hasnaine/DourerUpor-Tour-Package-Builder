<?php

// admin_packages.php
session_start();
// $_SESSION['admin'] = 'admin';
if(isset($_SESSION['email']) || isset($_SESSION['user_id'])) {
    unset($_SESSION['email']);
    unset($_SESSION['user_id']);
}// For testing purposes, set a user ID
require_once('db_connection\db.php');
include_once('DesignPatterns\approvalState.php');

// Handle package approval/rejection actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['package_id']) && isset($_POST['action'])) {
        $packageId = $_POST['package_id'];
        $action = $_POST['action'];
        
        $package = new Package($packageId);
        
        if ($action === 'approve') {
            $package->approve();
        } elseif ($action === 'reject') {
            $feedback = $_POST['rejection_feedback'] ?? 'Package does not meet our requirements.';
            $package->reject($feedback); // Pass feedback to the reject method
            header("Location: admin.php"); // Redirect to avoid resubmission
        }
    }
}

// Fetch all pending packages
$db = Database::getInstance();
$conn = $db->getConnection();
$stmt = $conn->prepare("
    SELECT p.package_id, p.package_name, p.publish_time, u.name as creator_name, p.details, p.image
    FROM packages p
    JOIN user u ON p.build_by = u.id
    WHERE p.status = 'pending'
    ORDER BY p.publish_time DESC
");
$stmt->execute();
$pendingPackages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get count of pending packages
$pendingState = new PendingState();
$pendingCount = $pendingState->getPendingCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Package Approval</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="admin.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="admin-container">
        <h1><i class="fas fa-user-shield"></i> Admin Dashboard</h1>
        
        <div class="admin-stats">
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <h3><?php echo $pendingCount; ?></h3>
                <p>Pending Packages</p>
            </div>
            <!-- Add other stats as needed -->
        </div>
        
        <div class="packages-container">
            <h2><i class="fas fa-suitcase"></i> Pending Packages</h2>
            
            <?php if (count($pendingPackages) > 0): ?>
                <?php foreach ($pendingPackages as $package): ?>
                    <div class="package-card">
                        <div class="package-header">
                            <h3><?php echo htmlspecialchars($package['package_name']); ?></h3>
                            <span class="package-date">Published: <?php echo date('M d, Y', strtotime($package['publish_time'])); ?></span>
                        </div>
                        
                        <div class="package-content">
                            <div class="package-image">
                                <img src="DesignPatterns/uploaded_img/<?php echo $package['image']; ?>" alt="Package Image">
                            </div>
                            
                            <div class="package-info">
                                <p><strong>Created by:</strong> <?php echo htmlspecialchars($package['creator_name']); ?></p>
                                <p class="package-description"><?php echo substr(htmlspecialchars($package['details']), 0, 200) . '...'; ?></p>
                                
                                <div class="package-actions">
                                    <a href="package.php?id=<?php echo $package['package_id']; ?>" class="btn-view" target="_blank">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="package_id" value="<?php echo $package['package_id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn-approve"><i class="fas fa-check"></i> Approve</button>
                                    </form>
                                    
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="package_id" value="<?php echo $package['package_id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn-reject"><i class="fas fa-times"></i> Reject</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-packages">
                    <i class="fas fa-check-circle"></i>
                    <p>No pending packages to review!</p>
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
</body>
</html>

<script>
    // Modal handling code
    const modal = document.getElementById("rejectionModal");
    const closeBtn = document.getElementsByClassName("close")[0];
    const cancelBtn = document.getElementById("cancelRejection");

    // When the user clicks on the reject button in the package list, open the modal
    document.querySelectorAll('.package-actions .btn-reject').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const packageId = this.closest('form').querySelector('input[name="package_id"]').value;
            document.getElementById('rejection_package_id').value = packageId;
            modal.style.display = "block";
        });
    });

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