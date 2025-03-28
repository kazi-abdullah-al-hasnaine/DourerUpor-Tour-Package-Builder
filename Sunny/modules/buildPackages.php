<?php 
session_start();
include_once '../db_connection/db.php';

// Database connection
$db = Database::getInstance();
$conn = $db->getConnection();

// Fetch destinations (filtering only for Bangladesh)
$destinations = [];
$stmt = $conn->prepare("SELECT destination_id, name, country, type, cost FROM destinations WHERE country = 'Bangladesh'");
$stmt->execute();
$destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $packageName = $_POST['package_name'];
    $buildBy = 'User'; // Replace with session user if available
    
    // Insert into packages table
    $stmt = $conn->prepare("INSERT INTO packages (package_name, publish_time, build_by, status) VALUES (?, NOW(), ?, 'Pending')");
    $stmt->execute([$packageName, $buildBy]);
    $packageId = $conn->lastInsertId();
    
    // Insert selected destinations into package_details
    if (!empty($_POST['destinations'])) {
        $stepNumber = 1;  // Starting step number for each selected destination
        
        foreach ($_POST['destinations'] as $index => $destinationId) {
            $moneySaved = $_POST['money_saved'][$index] ?? 0;
            $dayCount = $_POST['day_count'][$index] ?? 1;
            $pickup = !empty($_POST['pickup'][$index]) ? $_POST['pickup'][$index] : NULL;
            $transportType = !empty($_POST['transport_type'][$index]) ? $_POST['transport_type'][$index] : NULL;
            $cost = $_POST['cost'][$index] ?? 0;
            
            $stmt = $conn->prepare("INSERT INTO package_details (package_id, destination_id, step_number, money_saved, day_count, pickup, transport_type, cost) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
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
        /* General Body Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* Container for Form */
        h1 {
            text-align: center;
            color: #4CAF50;
            padding: 20px 0;
        }

        form {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Package Name Input */
        input[name="package_name"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        /* Step Container */
        #steps-container {
            margin-top: 20px;
        }

        .step {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fafafa;
        }

        .step input, .step select {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        /* Button Styling */
        button[type="button"], button[type="submit"] {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        button[type="button"]:hover, button[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Remove Button (For Step) */
        button[type="button"] {
            background-color: #f44336;
        }

        button[type="button"]:hover {
            background-color: #e53935;
        }

        /* Responsive Styling for Small Screens */
        @media screen and (max-width: 600px) {
            form {
                padding: 15px;
            }

            .step input, .step select {
                width: 100%;
            }

            button[type="button"], button[type="submit"] {
                width: 100%;
                padding: 12px;
            }
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
