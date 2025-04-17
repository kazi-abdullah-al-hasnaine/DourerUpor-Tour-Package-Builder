<?php
class Database {
    private static $instance = null;
    private $conn;

    private $host = "localhost";
    private $db   = "DourerUpor";
    private $user = "root";
    private $pass = "";

    // Private constructor prevents direct object creation
    private function __construct() {
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->db", $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Database Connected!<br>";
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    // Static method to get the single instance
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Getter for connection
    public function getConnection() {
        return $this->conn;
    }
}
?>
