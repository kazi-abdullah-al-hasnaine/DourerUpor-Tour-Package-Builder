<?php
// Subject (Observable) interface
interface Subject {
    public function attach(Observer $observer, $package_id);
    public function detach(Observer $observer, $package_id);
    public function notify($package_id, $message);
}

// Observer interface
interface Observer {
    public function update($package_id, $message);
}

// Concrete Subject implementation
class PackageSubject implements Subject {
    private $observers = [];
    private static $instance = null;
    
    // Singleton pattern to have one observer manager
    private function __construct() {}
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function attach(Observer $observer, $package_id) {
        if (!isset($this->observers[$package_id])) {
            $this->observers[$package_id] = [];
        }
        $this->observers[$package_id][] = $observer;
    }
    
    public function detach(Observer $observer, $package_id) {
        if (isset($this->observers[$package_id])) {
            foreach ($this->observers[$package_id] as $key => $obs) {
                if ($obs === $observer) {
                    unset($this->observers[$package_id][$key]);
                    break;
                }
            }
        }
    }
    
    public function notify($package_id, $message) {
        if (isset($this->observers[$package_id])) {
            foreach ($this->observers[$package_id] as $observer) {
                $observer->update($package_id, $message);
            }
        } else {
            error_log("No observers found for package_id: $package_id");
        }
    }
    
    // Load all followers as observers for a package
    public function loadFollowersAsObservers($package_id, $conn) {
        $stmt = $conn->prepare("SELECT user_id FROM package_followers WHERE package_id = ?");
        $stmt->execute([$package_id]);
        $followers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($followers as $follower) {
            $observer = new UserObserver($follower['user_id'], $conn);
            $this->attach($observer, $package_id);
        }
    }
}

// Concrete Observer implementation
class UserObserver implements Observer {
    private $user_id;
    private $conn;
    
    public function __construct($user_id, $conn) {
        $this->user_id = $user_id; 
        $this->conn = $conn;
    }
    
    public function update($package_id, $message) {
        // Insert notification into database
        $stmt = $this->conn->prepare("INSERT INTO notifications (user_id, package_id, message, created_at, is_read) VALUES (?, ?, ?, NOW(), 0)");
        $stmt->execute([$this->user_id, $package_id, $message]);
    }
}
?>