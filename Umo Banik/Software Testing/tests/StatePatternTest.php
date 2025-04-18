<?php
use PHPUnit\Framework\TestCase;

require_once 'db_connection\db.php';
require_once 'DesignPatterns\approvalState.php';

class StatePatternTest extends TestCase
{
    private $conn;
    private $packageId;

    protected function setUp(): void
    {
        $this->conn = Database::getInstance()->getConnection();
        $stmt = $this->conn->prepare("INSERT INTO packages (package_name, status, build_by) VALUES (?, ?, ?)");
        $stmt->execute(['Test Package', 'pending', 1]);
        $this->packageId = $this->conn->lastInsertId();
    }

    protected function tearDown(): void
    {
        $this->conn->prepare("DELETE FROM packages WHERE package_id = ?")->execute([$this->packageId]);
        $this->conn->prepare("DELETE FROM notifications WHERE package_id = ?")->execute([$this->packageId]);
    }

    public function testInitialStateIsPending()
    {
        echo "Running test: testInitialStateIsPending\n";
        $package = new Package($this->packageId);
        $state = $package->getStateName();
        echo "Initial State: $state\n";
        $this->assertEquals('pending', $state);
        
        // Test the count for pending state
        $count = $package->getStateCount();
        echo "Initial Pending Count: $count\n";
        $this->assertEquals(1, $count);
    }

    public function testApproveChangesStateToApproved()
    {
        echo "Running test: testApproveChangesStateToApproved\n";
        $package = new Package($this->packageId);
        $package->approve();
        $state = $package->getStateName();
        echo "Current State after approve: $state\n";
        $this->assertEquals('approved', $state);
        
        // Test the count for approved state
        $count = $package->getStateCount();
        echo "Approved Count: $count\n";
        $this->assertEquals(1, $count);
    }
    public function testRejectAfterApprovalFails()
    {
        echo "Running test: testRejectAfterApprovalFails\n"; // Debugging
        $package = new Package($this->packageId);
        $package->approve();
        echo "State after approval: " . $package->getStateName() . "\n"; // Debugging

        $result = $package->reject("Feedback");
        echo "Reject result: $result\n"; // Debugging
        $state = $package->getStateName();
        echo "State after reject attempt: $state\n"; // Debugging

        $this->assertFalse($result); // We expect it to fail 
        $this->assertEquals('approved', $state); // And stay 'approved'
    }

    public function testStateCountAfterStateChange()
    {
        echo "Running test: testStateCountAfterStateChange\n";
        $package = new Package($this->packageId);
        
        // Get initial pending count
        $initialCount = $package->getStateCount();
        echo "Initial Count in Pending State: $initialCount\n";
        
        // Approve package
        $package->approve();
        
        // Get count in approved state
        $approvedCount = $package->getStateCount();
        echo "Count in Approved State: $approvedCount\n";
        
        // Assert counts
        $this->assertEquals(0, $initialCount - 1); // One less in pending
        $this->assertEquals(1, $approvedCount); // One in approved
    }
}
