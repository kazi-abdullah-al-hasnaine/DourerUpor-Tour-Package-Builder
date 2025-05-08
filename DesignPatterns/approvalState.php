<?php

//State Interface - Serves as a common contract for all concrete state implementations.
interface PackageState {
    public function approve($package);
    public function reject($package);
    public function getCount(); 
    public function getStateName();
}

//Concrete State
class PendingState implements PackageState {
    public function approve($package) {
        $package->setState(new ApprovedState());
        return true;
    }
    
    public function reject($package) {
        $package->setState(new RejectedState());
        return true;
    }
    
    public function getCount() {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) FROM packages WHERE status = 'pending'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    public function getStateName() {
        return 'pending';
    }
}

//Concrete State
class ApprovedState implements PackageState {
    public function approve($package) {
        return false;
    }
    
    public function reject($package) {
        // $package->setState(new RejectedState());
        return false;
    }
    
    public function getCount() {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) FROM packages WHERE status = 'approved'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    public function getStateName() {
        return 'approved';
    }
}

//Concrete State
class RejectedState implements PackageState {
    public function approve($package) {
        $package->setState(new ApprovedState());
        return true;
    }
    
    public function reject($package) {
        return false;
    }
    
    public function getCount() {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) FROM packages WHERE status = 'rejected'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    public function getStateName() {
        return 'rejected';
    }
}


//Context - Acts as the primary class that interacts with the clients
class Package {
    private $packageId;
    private $packageName;
    private $state;
    private $conn;
    
    public function __construct($packageId = null) {
        $this->state = new PendingState();
        $this->conn = Database::getInstance()->getConnection();
        
        if ($packageId) {
            $this->packageId = $packageId;
            $this->loadPackage();
        }
    }
    
    public function getId() {
        return $this->packageId;
    }
    
    public function getPackageInfo($packageId) {
        $stmt = $this->conn->prepare("SELECT p.*, u.name as creator_name 
                                     FROM packages p 
                                     JOIN user u ON p.build_by = u.id 
                                     WHERE p.package_id = ?");
        $stmt->execute([$packageId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function loadPackage() {
        $stmt = $this->conn->prepare("SELECT package_name, status FROM packages WHERE package_id = ?");
        $stmt->execute([$this->packageId]);
        $package = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($package) {
            $this->packageName = $package['package_name'];
            
            // Set the appropriate state based on the database status
            switch($package['status']) {
                case 'approved':
                    $this->state = new ApprovedState();
                    break;
                case 'rejected':
                    $this->state = new RejectedState();
                    break;
                default:
                    $this->state = new PendingState();
            }
        }
    }
    
    public function setState(PackageState $state) {
        $this->state = $state;
        
        $stmt = $this->conn->prepare("UPDATE packages SET status = ? WHERE package_id = ?");
        $stmt->execute([$state->getStateName(), $this->packageId]);
    }
    
    public function approve() {
        if ($this->state->approve($this)) {
            // Notifying the package creator that their package was approved
            $this->notifyCreator('approved');
            return true;
        }
        return false;
    }
    
    public function reject($feedback = '') {
        if ($this->state->reject($this)) {
            // Updating the database with rejection feedback
            $stmt = $this->conn->prepare("UPDATE packages SET rejection_feedback = ? WHERE package_id = ?");
            $stmt->execute([$feedback, $this->packageId]);
            
            // Notifying the creator that their package was rejected with feedback
            $this->notifyCreator('rejected', $feedback);
            return true;
        }
        return false;
    }
    
    private function notifyCreator($action, $feedback = '') {
        // Get the user who built this package
        $stmt = $this->conn->prepare("SELECT build_by FROM packages WHERE package_id = ?");
        $stmt->execute([$this->packageId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $userId = $result['build_by'];
            
            if ($action === 'rejected' && !empty($feedback)) {
                $message = "Your package '{$this->packageName}' has been {$action}. Feedback: {$feedback}";
            } else {
                $message = "Your package '{$this->packageName}' has been {$action}.";
            }
            
            // Insert notification
            $stmt = $this->conn->prepare("INSERT INTO notifications (user_id, package_id, message, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$userId, $this->packageId, $message]);
        }
    }
    
    public function getStateCount() {
        return $this->state->getCount();
    }
    
    public function getStateName() {
        return $this->state->getStateName();
    }
}