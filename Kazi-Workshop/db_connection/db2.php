<?php
class Database {
    private static $conn = null;

    private $host = "localhost";
    private $db   = "DourerUpor";
    private $user = "root";
    private $pass = "";

    public function __construct() {
        try {
            if (self::$conn === null) {
                self::$conn = new PDO("mysql:host=$this->host;dbname=$this->db", $this->user, $this->pass);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                // echo "Database Connected!<br>";
            }
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$conn === null) {
            new self(); // this will initialize the connection
        }
        return self::$conn;
    }

    public function getConnection() {
        return self::$conn;
    }
}
?>
