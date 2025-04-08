<?php
session_start();

include_once('../db_connection/db.php');
include_once('../DesignPatterns/PackageBuilder.php');

// Database connection
$db = Database::getInstance();
$conn = $db->getConnection();

// Fetch destinations (filtering only for Bangladesh)
$destinations = [];
$stmt = $conn->prepare("SELECT destination_id, name, country, type, cost FROM destinations WHERE country = 'Bangladesh'");
$stmt->execute();
$destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$user_id = $_SESSION['user_id'] ?? 'unknown';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $packageName = $_POST['package_name'];
    $details = $_POST['details'];
    $buildBy = $user_id;
    $mode = $_POST['mode'] ?? 'basic';

    $stmt = $conn->query("SELECT MAX(package_id) as max_id FROM packages");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $nextPackageId = ($result['max_id'] ?? 0) + 1;

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

    $builder = new TourPackageBuilder();
    $director = new PackageDirector($builder);

    if ($mode === 'full') {
        $destinationIds = $_POST['destinations'] ?? [];
        $moneySaved = $_POST['money_saved'] ?? [];
        $dayCount = $_POST['day_count'] ?? [];
        $pickupIds = $_POST['pickup'] ?? [];
        $transportType = $_POST['transport_type'] ?? [];
        $transportCost = $_POST['transport_cost'] ?? [];

        $placeholders = implode(',', array_fill(0, count($destinationIds), '?'));
        $stmt = $conn->prepare("SELECT destination_id, name FROM destinations WHERE destination_id IN ($placeholders)");
        $stmt->execute($destinationIds);
        $selectedDestinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $director->buildFullPackage(
            $packageName,
            $destinationIds,
            $moneySaved,
            $dayCount,
            $pickupIds,
            $transportType,
            $transportCost,
            $details,
            $imageName
        );

        $package = $builder->getPackage();

        $stmt = $conn->prepare("INSERT INTO packages (package_id, package_name, publish_time, build_by, status, details, image) 
                                VALUES (?, ?, NOW(), ?, 'Pending', ?, ?)");
        $stmt->execute([$nextPackageId, $packageName, $buildBy, $details, $imageName]);

        $stepNumber = 1;
        for ($i = 0; $i < count($destinationIds); $i++) {
            $stmt = $conn->prepare("INSERT INTO package_details (package_id, destination_id, step_number, money_saved, day_count, pickup, transport_type, cost) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $nextPackageId,
                $destinationIds[$i],
                $stepNumber,
                $moneySaved[$i] ?? 0,
                $dayCount[$i] ?? 1,
                $pickupIds[$i] ?? null,
                $transportType[$i] ?? null,
                $transportCost[$i] ?? 0
            ]);
            $stepNumber++;
        }
    } else { // Basic mode
        // Use the dedicated method for basic package creation
        $director->buildBasicPackage(
            $packageName,
            $details,
            $imageName
        );

        $package = $builder->getPackage();

        $stmt = $conn->prepare("INSERT INTO packages (package_id, package_name, publish_time, build_by, status, details, image) 
                                VALUES (?, ?, NOW(), ?, 'Pending', ?, ?)");
        $stmt->execute([$nextPackageId, $packageName, $buildBy, $details, $imageName]);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --text-color: #333;
            --light-text: #666;
            --background-color: #f8f9fa;
            --card-bg: #ffffff;
            --border-color: #e9ecef;
            --success-color: #4cc9f0;
            --danger-color: #f72585;
            --font-primary: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            --transition: all 0.3s ease;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            --border-radius: 8px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-primary);
            color: var(--text-color);
            background-color: var(--background-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        h1 {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--primary-color);
            font-weight: 600;
        }

        .form-container {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 2rem;
        }

        .toggle-container {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
            background-color: var(--background-color);
            border-radius: calc(var(--border-radius) - 2px);
            padding: 0.5rem;
        }

        .toggle-btn {
            background-color: transparent;
            color: var(--light-text);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: calc(var(--border-radius) - 4px);
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
        }

        .toggle-btn.active {
            background-color: var(--primary-color);
            color: white;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-color);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-family: inherit;
            font-size: 1rem;
            color: var(--text-color);
            background-color: white;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .form-control::placeholder {
            color: #adb5bd;
        }

        .input-icon-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            color: var(--light-text);
        }

        .has-icon {
            padding-left: 2.5rem;
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input-label {
            display: block;
            padding: 0.75rem 1rem;
            background-color: var(--background-color);
            border: 1px dashed var(--border-color);
            border-radius: var(--border-radius);
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .file-input-label:hover {
            background-color: #e9ecef;
        }

        .file-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-name {
            margin-top: 0.5rem;
            text-align: center;
            font-size: 0.875rem;
            color: var(--light-text);
        }

        .steps-container {
            margin-top: 2rem;
        }

        .step {
            background-color: var(--background-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .step h4 {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            color: var(--primary-color);
        }

        .step h4 i {
            margin-right: 0.5rem;
        }

        .step-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .step-full {
            grid-column: 1 / -1;
        }

        .remove-step {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background-color: transparent;
            border: none;
            color: var(--danger-color);
            cursor: pointer;
            transition: var(--transition);
            width: auto;
            padding: 0.25rem;
        }

        .remove-step:hover {
            transform: scale(1.1);
        }

        .btn {
            display: inline-block;
            font-weight: 500;
            text-align: center;
            vertical-align: middle;
            user-select: none;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            opacity: 0.9;
        }

        .btn:disabled {
            background-color: #adb5bd;
            cursor: not-allowed;
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }

        .full-mode {
            display: none;
        }

        @media (max-width: 768px) {
            .step-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <script>
        let destinationList = <?php echo json_encode($destinations); ?>;

        function toggleMode(mode) {
            const basicBtn = document.getElementById('basic-mode-btn');
            const fullBtn = document.getElementById('full-mode-btn');
            const fullModeSection = document.getElementById('full-mode-section');
            const modeInput = document.getElementById('mode-input');
            const publishButton = document.getElementById('publish-button');
            
            if (mode === 'basic') {
                basicBtn.classList.add('active');
                fullBtn.classList.remove('active');
                fullModeSection.style.display = 'none';
                modeInput.value = 'basic';
                publishButton.disabled = false;
            } else {
                basicBtn.classList.remove('active');
                fullBtn.classList.add('active');
                fullModeSection.style.display = 'block';
                modeInput.value = 'full';
                checkPublishButton();
            }
        }

        function addStep() {
            let stepCount = document.querySelectorAll('.step').length + 1;
            let container = document.getElementById("steps-container");
            let step = document.createElement("div");
            step.className = "step";
            step.innerHTML = `
                <h4><i class="fas fa-map-marker-alt"></i> Step ${stepCount}</h4>
                <button type="button" class="remove-step" onclick="this.parentElement.remove(); renumberSteps(); checkPublishButton();"><i class="fas fa-times"></i></button>
                
                <div class="step-grid">
                    <div class="form-group step-full">
                        <label for="destination-${stepCount}">Destination</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-search input-icon"></i>
                            <input type="text" id="destination-${stepCount}" class="form-control has-icon" placeholder="Search destination" oninput="selectDestination(this)" list="dest-list" required>
                            <datalist id="dest-list">
                                ${destinationList.map(dest => `<option data-id="${dest.destination_id}" data-cost="${dest.cost}" value="${dest.name}"></option>`).join('')}
                            </datalist>
                        </div>
                        <input type="hidden" name="destinations[]">
                    </div>
                    
                    <div class="form-group">
                        <label>Destination Cost</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-dollar-sign input-icon"></i>
                            <input type="number" name="cost[]" class="form-control has-icon" placeholder="Cost" readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Money Saved</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-piggy-bank input-icon"></i>
                            <input type="number" name="money_saved[]" class="form-control has-icon" placeholder="Amount saved" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Day Count</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-calendar-day input-icon"></i>
                            <input type="number" name="day_count[]" class="form-control has-icon" placeholder="Number of days" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Pickup Point</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-map-pin input-icon"></i>
                            <input type="text" class="form-control has-icon" placeholder="Search pickup point" oninput="selectPickup(this)" list="pickup-list">
                            <datalist id="pickup-list">
                                ${destinationList.map(dest => `<option data-id="${dest.destination_id}" value="${dest.name}"></option>`).join('')}
                            </datalist>
                        </div>
                        <input type="hidden" name="pickup[]">
                    </div>
                    
                    <div class="form-group">
                        <label>Transport Type</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-bus input-icon"></i>
                            <input type="text" name="transport_type[]" class="form-control has-icon" placeholder="Bus, Train, etc.">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Transport Cost</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-dollar-sign input-icon"></i>
                            <input type="number" name="transport_cost[]" class="form-control has-icon" placeholder="Transport cost">
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(step);
            checkPublishButton();
        }

        function renumberSteps() {
            const steps = document.querySelectorAll('.step h4');
            steps.forEach((step, index) => {
                step.innerHTML = `<i class="fas fa-map-marker-alt"></i> Step ${index + 1}`;
            });
        }

        function selectDestination(input) {
            let parentElement = input.closest('.step');
            let hiddenInput = parentElement.querySelector('input[name="destinations[]"]');
            let costInput = parentElement.querySelector('input[name="cost[]"]');
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
            let parentElement = input.closest('.step');
            let hiddenPickup = parentElement.querySelector('input[name="pickup[]"]');
            let options = document.getElementById("pickup-list").options;
            
            for (let option of options) {
                if (option.value === input.value) {
                    hiddenPickup.value = option.dataset.id;
                    break;
                }
            }
        }

        function checkPublishButton() {
            const modeInput = document.getElementById('mode-input');
            if (modeInput.value === 'basic') {
                return; // Always enabled in basic mode
            }
            
            let btn = document.getElementById('publish-button');
            let steps = document.getElementById('steps-container');
            btn.disabled = steps.children.length === 0;
        }

        function updateFileName() {
            const fileInput = document.getElementById('package-image');
            const fileName = document.getElementById('file-name');
            
            if (fileInput.files.length > 0) {
                fileName.textContent = fileInput.files[0].name;
            } else {
                fileName.textContent = '';
            }
        }

        window.onload = function() {
            toggleMode('basic'); // Start in basic mode
        }
    </script>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-suitcase"></i> Build Your Travel Package</h1>
        
        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" id="mode-input" name="mode" value="basic">
                
                <div class="toggle-container">
                    <button type="button" id="basic-mode-btn" class="toggle-btn" onclick="toggleMode('basic')">
                        <i class="fas fa-cube"></i> Basic Mode
                    </button>
                    <button type="button" id="full-mode-btn" class="toggle-btn" onclick="toggleMode('full')">
                        <i class="fas fa-cubes"></i> Full Mode
                    </button>
                </div>
                
                <div class="form-group">
                    <label for="package-name">Package Name</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-tag input-icon"></i>
                        <input type="text" id="package-name" name="package_name" class="form-control has-icon" placeholder="Enter package name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="package-details">Package Details</label>
                    <textarea id="package-details" name="details" class="form-control" placeholder="Describe your package..." required></textarea>
                </div>

                <div class="form-group">
                    <label>Package Image</label>
                    <div class="file-input-wrapper">
                        <label class="file-input-label">
                            <i class="fas fa-cloud-upload-alt"></i> Choose Image
                            <input type="file" id="package-image" name="package_image" class="file-input" accept=".jpg,.jpeg,.png" required onchange="updateFileName()">
                        </label>
                        <div id="file-name" class="file-name"></div>
                    </div>
                </div>

                <div id="full-mode-section" class="full-mode">
                    <h3><i class="fas fa-map-signs"></i> Destinations</h3>
                    <p>Add the destinations to include in your package.</p>
                    
                    <div id="steps-container" class="steps-container"></div>
                    
                    <button type="button" class="btn btn-outline" onclick="addStep()">
                        <i class="fas fa-plus"></i> Add Destination
                    </button>
                </div>
                
                <div class="action-buttons">
                    <button type="button" class="btn btn-outline" onclick="window.history.back()">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </button>
                    <button type="submit" id="publish-button" class="btn btn-success">
                        <i class="fas fa-paper-plane"></i> Publish Package
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>