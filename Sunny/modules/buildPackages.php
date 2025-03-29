<?php
session_start();

// Correct the file paths
include_once('../db_connection/db.php');  // For db.php, assuming it's one level up from modules
include_once('../DesignPatterns/PackageBuilder.php');  // For PackageBuilder.php, assuming it's in the DesignPatterns folder

// Database connection
$db = Database::getInstance();
$conn = $db->getConnection();

// Fetch destinations (filtering only for Bangladesh)
$destinations = [];
$stmt = $conn->prepare("SELECT destination_id, name, country, type, cost FROM destinations WHERE country = 'Bangladesh'");
$stmt->execute();
$destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $packageName = $_POST['package_name'];
    $buildBy = 'User'; // Replace with session user if available

    // Initialize the builder and director
    $builder = new TourPackageBuilder();
    $director = new PackageDirector($builder);
    $director->buildPackage(); // This will add destinations, money saved, day count, etc.

    // Get the constructed package
    $package = $builder->getPackage();

    // Insert into packages table
    $stmt = $conn->prepare("INSERT INTO packages (package_name, publish_time, build_by, status) VALUES (?, NOW(), ?, 'Pending')");
    $stmt->execute([$packageName, $buildBy]);
    $packageId = $conn->lastInsertId();

    // Insert selected destinations into package_details
    if (!empty($_POST['destinations'])) {
        $stepNumber = 1;  // Starting step number for each selected destination
        foreach ($_POST['destinations'] as $index => $destinationId) {
            $moneySaved = $package->moneySaved[$index] ?? 0;
            $dayCount = $package->dayCount[$index] ?? 1;
            $pickup = !empty($package->pickup[$index]) ? $package->pickup[$index] : NULL;
            $transportType = !empty($package->transportType[$index]) ? $package->transportType[$index] : NULL;
            $cost = $package->cost[$index] ?? 0;

            $stmt = $conn->prepare("INSERT INTO package_details (package_id, destination_id, step_number, money_saved, day_count, pickup, transport_type, cost) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$packageId, $destinationId, $stepNumber, $moneySaved, $dayCount, $pickup, $transportType, $cost]);
            $stepNumber++;  // Increment step number for the next destination
        }
    }

    echo "Package successfully created!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Build a Package</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        form {
            width: 50%;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .step {
            margin-bottom: 20px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:disabled {
            background-color: #ccc;
        }
    </style>
    <script>
        let destinationsData = <?php echo json_encode($destinations); ?>;

        function addStep() {
            let stepsContainer = document.getElementById("steps-container");
            let stepHtml = `
                <div class="step">
                    <select name="destinations[]" onchange="populateFields(this)" required>
                        <option value="" disabled selected>Select Destination</option>
                        <?php foreach ($destinations as $destination): ?>
                            <option value="<?php echo $destination['destination_id']; ?>" data-name="<?php echo htmlspecialchars($destination['name']); ?>" data-country="<?php echo htmlspecialchars($destination['country']); ?>" data-type="<?php echo htmlspecialchars($destination['type']); ?>" data-cost="<?php echo htmlspecialchars($destination['cost']); ?>">
                                <?php echo htmlspecialchars($destination['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="destination_name[]" placeholder="Destination Name" readonly>
                    <input type="text" name="country[]" placeholder="Country" readonly>
                    <input type="text" name="type[]" placeholder="Type" readonly>
                    <input type="number" name="cost[]" placeholder="Cost" readonly>
                    <input type="number" name="money_saved[]" placeholder="Money Saved" required>
                    <input type="number" name="day_count[]" placeholder="Days" required>
                    <input type="text" name="pickup[]" placeholder="Pickup (Optional)">
                    <input type="text" name="transport_type[]" placeholder="Transport Type (Optional)">
                    <button type="button" onclick="this.parentElement.remove(); checkPublishButton();">Remove</button>
                </div>`;
            stepsContainer.insertAdjacentHTML('beforeend', stepHtml);
            checkPublishButton();
        }

        function populateFields(selectElement) {
            let selectedOption = selectElement.options[selectElement.selectedIndex];
            let stepDiv = selectElement.parentElement;
            stepDiv.querySelector("input[name='destination_name[]']").value = selectedOption.getAttribute("data-name");
            stepDiv.querySelector("input[name='country[]']").value = selectedOption.getAttribute("data-country");
            stepDiv.querySelector("input[name='type[]']").value = selectedOption.getAttribute("data-type");
            stepDiv.querySelector("input[name='cost[]']").value = selectedOption.getAttribute("data-cost");
        }

        function checkPublishButton() {
            let publishButton = document.getElementById("publish-button");
            let stepsContainer = document.getElementById("steps-container");
            publishButton.disabled = stepsContainer.children.length === 0;
        }
    </script>
</head>
<body>
    <h1>Build Your Own Package</h1>
    <form method="POST">
        <input type="text" name="package_name" placeholder="Package Name" required>
        <div id="steps-container"></div>
        <button type="button" onclick="addStep()">Add Destination</button>
        <button type="submit" id="publish-button" disabled>Publish</button>
    </form>
</body>
</html>
