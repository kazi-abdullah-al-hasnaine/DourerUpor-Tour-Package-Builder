<?php
// filepath: c:\xampp\htdocs\DourerUpor-Tour-Package-Builder\tests\DatabaseTest.php


use PHPUnit\Framework\TestCase;

require_once 'c:\xampp\htdocs\DourerUpor-Tour-Package-Builder\Kazi-Workshop\db_connection\db.php';

class DatabaseTest extends TestCase {
    public function testSingletonInstance() {
        $db1 = Database::getInstance(); // database 1
        $db2 = Database::getInstance(); // database 1

        // Assert that both instances are the same
        $this->assertSame($db1, $db2);
    }

    public function testConnectionIsPDO() {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        // Assert that the connection is a PDO instance
        $this->assertInstanceOf(PDO::class, $conn); // নিশ্চিত করে যে $conn একটি PDO অবজেক্ট। 
    }
}