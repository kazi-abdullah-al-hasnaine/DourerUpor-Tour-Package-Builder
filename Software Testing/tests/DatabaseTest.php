<?php
// filepath: c:\xampp\htdocs\DourerUpor-Tour-Package-Builder\tests\DatabaseTest.php

// Starting database tests


use PHPUnit\Framework\TestCase;

require_once 'c:\xampp\htdocs\DourerUpor-Tour-Package-Builder\main\db_connection\db.php';

class DatabaseTest extends TestCase {
    public function testSingletonInstance() {
        $db1 = Database::getInstance(); // database 1
        $db2 = Database::getInstance(); // database 1
        // Assert that both instances are the same
        $this->assertSame($db1, $db2);
    }

}
