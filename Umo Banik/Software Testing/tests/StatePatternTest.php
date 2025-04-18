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
        $this->assertIsInt($count);
    }

    public function testPendingStateTransitions()
    {
        echo "Running test: testPendingStateTransitions\n";
        $package = new Package($this->packageId);
        
        // Test initial state
        $this->assertEquals('pending', $package->getStateName());
        
        // Test approve transition
        $result = $package->approve();
        $this->assertTrue($result);
        $this->assertEquals('approved', $package->getStateName());
        
        // Reset for next test
        $this->conn->prepare("UPDATE packages SET status = 'pending' WHERE package_id = ?")->execute([$this->packageId]);
        $package = new Package($this->packageId);
        
        // Test reject transition
        $result = $package->reject("Test feedback");
        $this->assertTrue($result);
        $this->assertEquals('rejected', $package->getStateName());
        
        // Test setPending on pending state (should fail)
        $this->conn->prepare("UPDATE packages SET status = 'pending' WHERE package_id = ?")->execute([$this->packageId]);
        $package = new Package($this->packageId);
        $result = $package->setPending();
        $this->assertFalse($result);
        $this->assertEquals('pending', $package->getStateName());
    }

    public function testApprovedStateTransitions()
    {
        echo "Running test: testApprovedStateTransitions\n";
        $package = new Package($this->packageId);
        
        // Set to approved state first
        $package->approve();
        $this->assertEquals('approved', $package->getStateName());
        
        // Test approve on approved state (should fail)
        $result = $package->approve();
        $this->assertFalse($result);
        $this->assertEquals('approved', $package->getStateName());
        
        // Test reject on approved state (should fail)
        $result = $package->reject("Test feedback");
        $this->assertFalse($result);
        $this->assertEquals('approved', $package->getStateName());
        
        // Test setPending on approved state (should succeed)
        $result = $package->setPending();
        $this->assertTrue($result);
        $this->assertEquals('pending', $package->getStateName());
    }

    public function testRejectedStateTransitions()
    {
        echo "Running test: testRejectedStateTransitions\n";
        $package = new Package($this->packageId);
        
        // Set to rejected state first
        $package->reject("Initial rejection");
        $this->assertEquals('rejected', $package->getStateName());
        
        // Test approve on rejected state (should succeed)
        $result = $package->approve();
        $this->assertTrue($result);
        $this->assertEquals('approved', $package->getStateName());
        
        // Reset to rejected state
        $this->conn->prepare("UPDATE packages SET status = 'rejected' WHERE package_id = ?")->execute([$this->packageId]);
        $package = new Package($this->packageId);
        
        // Test reject on rejected state (should fail)
        $result = $package->reject("More feedback");
        $this->assertFalse($result);
        $this->assertEquals('rejected', $package->getStateName());
        
        // Test setPending on rejected state (should succeed)
        $result = $package->setPending();
        $this->assertTrue($result);
        $this->assertEquals('pending', $package->getStateName());
    }

    public function testNotificationCreation()
    {
        echo "Running test: testNotificationCreation\n";
        $package = new Package($this->packageId);
        
        // Approve and check for notification
        $package->approve();
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM notifications WHERE package_id = ? AND message LIKE '%approved%'");
        $stmt->execute([$this->packageId]);
        $count = $stmt->fetchColumn();
        $this->assertEquals(1, $count, "Approval notification not created");
        
        // Reset to pending
        $this->conn->prepare("UPDATE packages SET status = 'pending' WHERE package_id = ?")->execute([$this->packageId]);
        $package = new Package($this->packageId);
        
        // Reject with feedback and check for notification
        $feedback = "Test rejection feedback";
        $package->reject($feedback);
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM notifications WHERE package_id = ? AND message LIKE ?");
        $stmt->execute([$this->packageId, "%$feedback%"]);
        $count = $stmt->fetchColumn();
        $this->assertEquals(1, $count, "Rejection notification with feedback not created");
    }

    public function testStateCountReflectsDatabase()
    {
        echo "Running test: testStateCountReflectsDatabase\n";
        
        // Insert another test package
        $stmt = $this->conn->prepare("INSERT INTO packages (package_name, status, build_by) VALUES (?, ?, ?)");
        $stmt->execute(['Another Test Package', 'pending', 1]);
        $anotherPackageId = $this->conn->lastInsertId();
        
        // Get pending count
        $package = new Package($this->packageId);
        $pendingCount = $package->getStateCount();
        
        // Approve one package
        $package->approve();
        
        // Get approved count
        $approvedCount = $package->getStateCount();
        
        // Get pending count again with another package
        $anotherPackage = new Package($anotherPackageId);
        $newPendingCount = $anotherPackage->getStateCount();
        
        // Clean up
        $this->conn->prepare("DELETE FROM packages WHERE package_id = ?")->execute([$anotherPackageId]);
        
        // Verify counts
        $this->assertEquals($pendingCount - 1, $newPendingCount, "Pending count should decrease by 1");
        $this->assertEquals(1, $approvedCount, "Approved count should be 1");
    }
}