<?php
session_start();

include_once('../db_connection/db.php');
include_once('../DesignPatterns/PackageBuilder.php');

// Database connection
$db = Database::getInstance();
$conn = $db->getConnection();

// Fetch destinations (only for Bangladesh)
$stmt = $conn->prepare("SELECT destination_id, name, country, type, cost FROM destinations WHERE country = 'Bangladesh'");
$stmt->execute();
$destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$user_id = $_SESSION['user_id'] ?? 'unknown';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $packageName = $_POST['package_name'];
    $details = $_POST['details'];
    $buildBy = $user_id;

    // Get next package ID
    $stmt = $conn->query("SELECT MAX(package_id) as max_id FROM packages");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $nextPackageId = ($result['max_id'] ?? 0) + 1;

    // Image upload
    $imageName = $nextPackageId . '.jpg';
    $imagePath = "../DesignPatterns/uploaded_img/" . $imageName;
    if (!empty($_FILES['package_image']['name'])) {
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $fileExtension = strtolower(pathinfo($_FILES['package_image']['name'], PATHINFO_EXTENSION));
        if (in_array($fileExtension, $allowedExtensions)) {
            move_uploaded_file($_FILES['package_image']['tmp_name'], $imagePath);
        } else {
            echo "Invalid file type. Only JPG and PNG are allowed.";
            exit;
        }
    }

    // Insert into packages table
    $stmt = $conn->prepare("INSERT INTO packages (package_id, package_name, publish_time, build_by, status, details, image) 
                            VALUES (?, ?, NOW(), ?, 'Pending', ?, ?)");
    $stmt->execute([$nextPackageId, $packageName, $buildBy, $details, $imageName]);

    // Insert into package_details
    $stepNumber = 1;
    foreach ($_POST['destinations'] as $index => $destinationId) {
        $pickupId = $_POST['pickup'][$index] ?? NULL;
        $transportType = $_POST['transport_type'][$index] ?? NULL;
        $transportCost = $_POST['transport_cost'][$index] ?? 0;
        $moneySaved = $_POST['money_saved'][$index] ?? 0;
        $dayCount = $_POST['day_count'][$index] ?? 1;

        $stmt = $conn->prepare("INSERT INTO package_details 
            (package_id, destination_id, step_number, money_saved, day_count, pickup, transport_type, cost) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $nextPackageId,
            $destinationId,
            $stepNumber,
            $moneySaved,
            $dayCount,
            $pickupId,
            $transportType,
            $transportCost
        ]);
        $stepNumber++;
    }

    echo "Package successfully created!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Build a Package</title>
    <style>
        body { font-family: Arial; }
        form { width: 50%; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        .step, .details-container, .image-container { margin-bottom: 20px; }
        input, select, textarea { width: 100%; padding: 10px; margin: 5px 0; border-radius: 4px; border: 1px solid #ccc; }
        input[readonly] { background-color: #f0f0f0; }
        button { background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:disabled { background-color: #ccc; }
    </style>
    <script>
        let destinationList = <?php echo json_encode($destinations); ?>;

        function addStep() {
            let container = document.getElementById("steps-container");
            let step = document.createElement("div");
            step.className = "step";
            step.innerHTML = `
                <h4>Step</h4>
                <input type="text" placeholder="Search Destination" oninput="selectDestination(this)" list="dest-list" required>
                <datalist id="dest-list">
                    ${destinationList.map(dest => `<option data-id="${dest.destination_id}" data-cost="${dest.cost}" value="${dest.name}"></option>`).join('')}
                </datalist>
                <input type="hidden" name="destinations[]">
                <input type="number" name="cost[]" placeholder="Destination Cost" readonly>

                <input type="text" placeholder="Search Pickup Point" oninput="selectPickup(this)" list="pickup-list">
                <datalist id="pickup-list">
                    ${destinationList.map(dest => `<option data-id="${dest.destination_id}" value="${dest.name}"></option>`).join('')}
                </datalist>
                <input type="hidden" name="pickup[]">

                <input type="text" name="transport_type[]" placeholder="Transport Type (Optional)">
                <input type="number" name="transport_cost[]" placeholder="Transport Cost (manual input)">
                <input type="number" name="money_saved[]" placeholder="Money Saved" required>
                <input type="number" name="day_count[]" placeholder="Day Count" required>
                <button type="button" onclick="this.parentElement.remove(); checkPublishButton();">Remove</button>
            `;
            container.appendChild(step);
            checkPublishButton();
        }

        function selectDestination(input) {
            let siblings = Array.from(input.parentElement.children);
            let hiddenInput = siblings.find(el => el.name === 'destinations[]');
            let costInput = siblings.find(el => el.name === 'cost[]');
            let options = document.getElementById("dest-list").options;
            for (let option of options) {
                if (option.value === input.value) {
                    hiddenInput.value = option.dataset.id;
                    costInput.value = option.dataset.cost;
                    break;
                }
            }
        }

        function selectPickup(input) {
            let siblings = Array.from(input.parentElement.children);
            let hiddenPickup = siblings.find(el => el.name === 'pickup[]');
            let options = document.getElementById("pickup-list").options;
            for (let option of options) {
                if (option.value === input.value) {
                    hiddenPickup.value = option.dataset.id;
                    break;
                }
            }
        }

        function checkPublishButton() {
            let btn = document.getElementById("publish-button");
            let steps = document.getElementById("steps-container");
            btn.disabled = steps.children.length === 0;
        }
    </script>
</head>
<body>
    <h1>Build Your Own Package</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="package_name" placeholder="Package Name" required>

        <div class="details-container">
            <h3>Package Details</h3>
            <textarea name="details" rows="5" placeholder="Describe your package..." required></textarea>
        </div>

        <div class="image-container">
            <h3>Upload Package Image</h3>
            <input type="file" name="package_image" accept=".jpg,.jpeg,.png" required>
        </div>

        <div id="steps-container"></div>
        <button type="button" onclick="addStep()">Add Destination</button>
        <button type="submit" id="publish-button" disabled>Publish</button>
    </form>
</body>
</html>
