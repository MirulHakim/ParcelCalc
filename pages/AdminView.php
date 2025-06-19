<?php
session_start();

// CSRF token setup
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Flash messages
$successMsg = '';
$errorMsg = '';
if (isset($_SESSION['success'])) {
    $successMsg = $_SESSION['success'];
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    $errorMsg = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Check login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: Login.php");
    exit();
}

require_once "pdo.php";

// Function to generate auto-incrementing parcel ID
function generateParcelId($pdo)
{
    $day = date('d');
    $month = strtoupper(date('M'));
    $todayPrefix = $day . $month . '/';
    $pattern = $todayPrefix . '%';

    // Check if we have a session key for today's last generated ID
    $sessionKey = 'last_generated_' . $day . $month;

    // If we already generated an ID in this session, increment it
    if (isset($_SESSION[$sessionKey])) {
        $lastNumber = $_SESSION[$sessionKey];
        $nextNumber = $lastNumber + 1;
    } else {
        // First time generating for today, check database
        $stmt = $pdo->prepare("SELECT Parcel_id FROM Parcel_info WHERE Parcel_id LIKE :pattern");
        $stmt->execute([':pattern' => $pattern]);
        $allParcels = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Only keep IDs that start with the exact prefix (case-insensitive)
            if (stripos($row['Parcel_id'], $todayPrefix) === 0) {
                $allParcels[] = $row['Parcel_id'];
            }
        }
        // Extract numbers
        $existingNumbers = [];
        foreach ($allParcels as $parcelId) {
            if (preg_match('/\/(\d+)$/', $parcelId, $matches)) {
                $existingNumbers[] = intval($matches[1]);
            }
        }
        if (empty($existingNumbers)) {
            $nextNumber = 1;
        } else {
            sort($existingNumbers);
            $nextNumber = 1;
            foreach ($existingNumbers as $num) {
                if ($num == $nextNumber) {
                    $nextNumber++;
                } else {
                    break;
                }
            }
        }
    }
    // Do NOT store this number in session here!
    return $todayPrefix . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
}

// Function to get preview of next parcel ID (for display only)
function getNextParcelIdPreview($pdo)
{
    $day = date('d');
    $month = strtoupper(date('M'));
    $todayPrefix = $day . $month . '/';
    $pattern = $todayPrefix . '%';

    $stmt = $pdo->prepare("SELECT Parcel_id FROM Parcel_info WHERE Parcel_id LIKE :pattern");
    $stmt->execute([':pattern' => $pattern]);
    $allParcels = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Only keep IDs that start with the exact prefix (case-insensitive)
        if (stripos($row['Parcel_id'], $todayPrefix) === 0) {
            $allParcels[] = $row['Parcel_id'];
        }
    }

    // Extract numbers from existing parcel IDs
    $existingNumbers = [];
    foreach ($allParcels as $parcelId) {
        if (preg_match('/\/(\d+)$/', $parcelId, $matches)) {
            $existingNumbers[] = intval($matches[1]);
        }
    }

    // Find the next available number
    if (empty($existingNumbers)) {
        $nextNumber = 1;
    } else {
        sort($existingNumbers);
        $nextNumber = 1;

        // Find the first gap or the next number after the highest
        foreach ($existingNumbers as $num) {
            if ($num == $nextNumber) {
                $nextNumber++;
            } else {
                break; // Found a gap, use this number
            }
        }
    }

    // Format: 19JUN/01 (with leading zero for numbers < 10)
    return $todayPrefix . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        !isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("Invalid CSRF token.");
    }

    if (isset($_POST['delete'])) {
        $delete_id = $_POST['delete_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM Parcel_info WHERE Parcel_id = :parcel_id");
            $stmt->execute([':parcel_id' => $delete_id]);
            $_SESSION['success'] = "Parcel deleted successfully.";
            header("Location: AdminView.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = "Delete Error: " . $e->getMessage();
            header("Location: AdminView.php");
            exit();
        }

    } elseif (isset($_POST['search'])) {
        // Handled later in HTML section
    } elseif (isset($_POST['PhoneNum'], $_POST['Parcel_type'], $_POST['Parcel_owner'])) {
        $phone = $_POST['PhoneNum'];
        $parcel_type = $_POST['Parcel_type'];
        $owner = $_POST['Parcel_owner'];
        // Validate uploaded image
    if (
        !isset($_FILES['parcel_image']) ||
        $_FILES['parcel_image']['error'] !== UPLOAD_ERR_OK ||
        !getimagesize($_FILES['parcel_image']['tmp_name'])
    ) {
        $_SESSION['error'] = "Please upload a valid parcel image.";
        header("Location: AdminView.php");
        exit(); 
    }
    $image = file_get_contents($_FILES['parcel_image']['tmp_name']);

        // Debug: Let's see what's happening
        error_log("=== FORM SUBMISSION DEBUG ===");
        error_log("Phone: $phone, Type: $parcel_type, Owner: $owner");

        // Generate auto-incrementing parcel ID (first attempt uses session)
        $parcel_id = generateParcelId($pdo);
        error_log("Generated Parcel ID: $parcel_id");

        $inserted = false;
        $retry = 0;
        $max_retries = 5;
        $errorMsg = '';
        while (!$inserted && $retry < $max_retries) {
            error_log("Attempt #$retry with Parcel ID: $parcel_id");
            try {
                $stmt = $pdo->prepare("INSERT INTO Parcel_info (PhoneNum, Parcel_type, Parcel_owner, Parcel_id, Date_arrived, Date_received, Parcel_image, Status)  
                           VALUES (:phone, :type, :owner, :parcel_id, NOW(), NULL, :image, 0)");
                $stmt->execute([
                    ':phone' => $phone,
                    ':type' => $parcel_type,
                    ':owner' => $owner,
                    ':parcel_id' => $parcel_id,
                    ':image' => $image,
                ]);
                error_log("Parcel inserted successfully with ID: $parcel_id");
                $_SESSION['success'] = "✅ Parcel added successfully with ID: " . $parcel_id;
                // Only update session after successful insert
                $sessionKey = 'last_generated_' . date('d') . strtoupper(date('M'));
                if (preg_match('/\/(\d+)$/', $parcel_id, $matches)) {
                    $_SESSION[$sessionKey] = intval($matches[1]);
                }
                $inserted = true;
            } catch (PDOException $e) {
                $errorMsg = $e->getMessage();
                error_log("Insert Error: " . $errorMsg);
                // Log all existing parcel IDs for today
                $day = date('d');
                $month = strtoupper(date('M'));
                $patterns = [
                    $day . ' ' . $month . '/%',
                    $day . ' ' . ucfirst(strtolower($month)) . '/%',
                    $day . $month . '/%',
                    $day . ucfirst(strtolower($month)) . '/%'
                ];
                $allParcels = [];
                foreach ($patterns as $pattern) {
                    $stmt2 = $pdo->prepare("SELECT Parcel_id FROM Parcel_info WHERE Parcel_id LIKE :pattern");
                    $stmt2->execute([':pattern' => $pattern]);
                    while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                        $allParcels[] = $row['Parcel_id'];
                    }
                }
                error_log("All found parcels for today: " . implode(', ', $allParcels));
                if (strpos($errorMsg, 'Duplicate') !== false || strpos($errorMsg, '1062') !== false) {
                    // Duplicate Parcel ID, try again
                    $_SESSION['error'] = "Sorry, this parcel ID already exists. Retrying with a new ID... (" . htmlspecialchars($errorMsg) . ")";
                    // On retry, always recalculate from DB and do NOT use session
                    $parcel_id = generateParcelIdNoSession($pdo);
                    $retry++;
                } else {
                    $_SESSION['error'] = "Insert Error: " . $errorMsg;
                    break;
                }
            }
        }
        if (!$inserted) {
            $_SESSION['error'] = $errorMsg ?: "Insert Error: Could not add parcel after multiple attempts.";
        }
        header("Location: AdminView.php");
        exit();
    }
}

// Helper function: always calculate next available Parcel ID from DB, ignoring session
function generateParcelIdNoSession($pdo)
{
    $day = date('d');
    $month = strtoupper(date('M'));
    $todayPrefix = $day . $month . '/';
    $pattern = $todayPrefix . '%';
    $stmt = $pdo->prepare("SELECT Parcel_id FROM Parcel_info WHERE Parcel_id LIKE :pattern");
    $stmt->execute([':pattern' => $pattern]);
    $allParcels = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (stripos($row['Parcel_id'], $todayPrefix) === 0) {
            $allParcels[] = $row['Parcel_id'];
        }
    }
    $existingNumbers = [];
    foreach ($allParcels as $parcelId) {
        if (preg_match('/\/(\d+)$/', $parcelId, $matches)) {
            $existingNumbers[] = intval($matches[1]);
        }
    }
    if (empty($existingNumbers)) {
        $nextNumber = 1;
    } else {
        sort($existingNumbers);
        $nextNumber = 1;
        foreach ($existingNumbers as $num) {
            if ($num == $nextNumber) {
                $nextNumber++;
            } else {
                break;
            }
        }
    }
    return $todayPrefix . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AdminView</title>
    <link rel="icon" type="image/x-icon" href="../resources/favicon.ico" />
    <link rel="stylesheet" href="../css/AdminView.css" />
    <link rel="stylesheet" href="../css/style.css" />
</head>

<body>
    <div class="header">
        <div class="row" style="gap: 0px">
            <div class="box blue" style="position: relative; z-index: 0"></div>
            <div class="box trapezium" style="position: relative; z-index: 1"></div>
            <div class="row logos">
                <img class="logo" src="../resources/Header/image-10.png" />
                <div class="x">X</div>
                <img class="logo" src="../resources/Header/logo-k-14-10.png" />
            </div>
        </div>
        <a href="logout.php">
            <button class="login-button">LOGOUT</button>
        </a>
    </div>

    <div class="sidebar">
        <a href="#">Menu</a>
        <a href="NewParcel.php">Add New Parcel</a>
        <a href="EditParcel.php">Edit/Delete Parcel Info</a>
        <form action="GeneratePDF.php" method="post" style="margin: 10px 0;">
            <button type="submit" name="submit">Create PDF</button>
        </form>
    </div>

    <div class="main-content">
        <?php
        if ($successMsg)
            echo "<p style='color: green; font-weight:bold;'>$successMsg</p>";
        if ($errorMsg)
            echo "<p style='color: red; font-weight:bold;'>$errorMsg</p>";
        ?>

        <h2>Add New Parcel</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <label for="phone">Phone Number:</label><br>
            <input type="text" id="phone" name="PhoneNum" placeholder="Enter Phone Number" required
                style="width: 88.3%;" /><br>

            <label for="parcel-type">Parcel Type:</label><br>
            <select id="parcel-type" name="Parcel_type" required style="width: 90%;">
                <option value="">Select Parcel Type</option>
                <option value="KOTAK">KOTAK</option>
                <option value="HITAM">HITAM</option>
                <option value="PUTIH">PUTIH</option>
                <option value="KELABU">KELABU</option>
                <option value="OTHERS">OTHERS</option>
            </select><br>

            <label for="owner">Parcel Owner:</label><br>
            <input type="text" id="owner" name="Parcel_owner" placeholder="Enter Owner's Name" required
                style="width: 88.2%;" /><br>

            <label for="image">Parcel Image</label>
            <div>
                <input type="file" id="image" name="parcel_image" accept="image/*" required/>
            </div>

            <p style="color: #666; font-style: italic;">
                Current Date: <?php echo date('Y-m-d H:i:s'); ?><br>
                Parcel ID will be automatically generated (<?php echo getNextParcelIdPreview($pdo); ?>)
            </p>

            <button type="submit" style="width: 90%; background: #495bbf;">Add to list</button>
        </form>
        <?php

        /* Fetch all parcels
        $stmt = $pdo->query("SELECT * FROM Parcel_info ORDER BY Parcel_id DESC"); // Adjust column name if needed
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="parcel-info">';
            echo '<div class="parcel-detail"><span>Owner\'s Name:</span><span>' . htmlspecialchars($row['Parcel_owner']) . '</span></div>';
            echo '<div class="parcel-detail"><span>Phone Number:</span><span>' . htmlspecialchars($row['PhoneNum']) . '</span></div>';
            echo '<div class="parcel-detail"><span>Parcel ID:</span><span>' . htmlspecialchars($row['Parcel_id']) . '</span></div>';
            echo '<form method="POST" action="" onsubmit="return confirm(\'Are you sure you want to delete this parcel?\');">';
            echo '<input type="hidden" name="delete_id" value="' . $row['Parcel_id'] . '">';
            echo '<button type="submit" name="delete" class="button delete-btn">🗑 Delete</button>';
            echo '</form>';
            echo '</div>';
        }*/
        ?>

        <h3>Parcel Info</h3>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="text" name="search_id" placeholder="Enter Parcel ID" required style="width: 88.3%;" />
            <button type="submit" name="search" style="width: 90%; background: #495bbf;">Search</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
            $search_id = $_POST['search_id'];

            try {
                $stmt = $pdo->prepare("SELECT * FROM Parcel_info WHERE Parcel_id = :parcel_id");
                $stmt->execute([':parcel_id' => $search_id]);
                $parcel = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($parcel) {
                    echo '<div class="parcel-info">';
                    echo '<div class="parcel-detail"><span>Owner\'s Name:</span><span>' . htmlspecialchars($parcel['Parcel_owner']) . '</span></div>';
                    echo '<div class="parcel-detail"><span>Arrive Date:</span><span>' . htmlspecialchars($parcel['Date_arrived'] ?? 'Not Available') . '</span></div>';
                    echo '<div class="parcel-detail"><span>Parcel ID:</span><span>' . htmlspecialchars($parcel['Parcel_id']) . '</span></div>';
                    echo '<div class="parcel-detail"><span>Phone Number:</span><span>' . htmlspecialchars($parcel['PhoneNum']) . '</span></div>';
                    //harga ikut current date 
                    $arrivedDate = $parcel['Date_arrived'] ?? '';
                    $jsArrivedDate = $arrivedDate ? $arrivedDate : date('Y-m-d');
                    $arrived = new DateTime($arrivedDate);
                    $today = new DateTime();
                    $interval = $today->diff($arrived);
                    $days = $interval->days;

                    $price = 1.00;
                    if ($today > $arrived && $days > 1) {
                        $price += ($days - 1) * 0.50;
                    }
                    echo '<div class="parcel-detail"><span>Price:</span><span>RM ' . number_format($price, 2) . '</span></div>';

                    $statusText = ($parcel['Status'] == 1 ? 'Claimed' : 'Unclaimed');
                    $statusColor = ($parcel['Status'] == 1 ? 'green' : 'red');
                    echo '<div class="parcel-detail"><span>Status:</span><span style="color:' . $statusColor . ';">' . htmlspecialchars($statusText) . '</span></div>';

                    echo '<div class="button-group">';
                    echo '<form method="POST" action="" onsubmit="return confirm(\'Delete this parcel?\');" style="display: inline;">';
                    echo '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
                    echo '<input type="hidden" name="delete_id" value="' . htmlspecialchars($parcel['Parcel_id']) . '">';
                    echo '<button type="submit" name="delete" class="button delete-btn">🗑 Delete</button>';
                    echo '</form>';
                    echo '</div></div>';
                } else {
                    echo "<div class='parcel-info'><p style='color:red;'>❌ No parcel found with ID: " . htmlspecialchars($search_id) . "</p></div>";
                }

            } catch (PDOException $e) {
                echo "<p>Error: " . $e->getMessage() . "</p>";
            }
        }
        ?>