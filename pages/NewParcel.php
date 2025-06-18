<?php
session_start();
require_once "pdo.php";

// Generate CSRF token if not already generated
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Function to generate auto-incrementing parcel ID
function generateParcelId($pdo) {
    $day = date('d'); // Day of month
    $month = strtoupper(date('M')); // Month abbreviation in uppercase
    
    // Check if we have a session key for today's last generated ID
    $sessionKey = 'last_generated_' . $day . $month;
    
    // If we already generated an ID in this session, increment it
    if (isset($_SESSION[$sessionKey])) {
        $lastNumber = $_SESSION[$sessionKey];
        $nextNumber = $lastNumber + 1;
    } else {
        // First time generating for today, check database
        // Get all parcel IDs for today with different possible formats
        $patterns = [
            $day . ' ' . $month . '/%',      // "19 JUN/%"
            $day . ' ' . ucfirst(strtolower($month)) . '/%',  // "19 Jun/%"
            $day . $month . '/%',            // "19JUN/%"
            $day . ucfirst(strtolower($month)) . '/%'         // "19Jun/%"
        ];
        
        $allParcels = [];
        foreach ($patterns as $pattern) {
            $stmt = $pdo->prepare("SELECT Parcel_id FROM Parcel_info WHERE Parcel_id LIKE :pattern");
            $stmt->execute([':pattern' => $pattern]);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
    }
    
    // Store this number in session for next time
    $_SESSION[$sessionKey] = $nextNumber;
    
    // Format: 19JUN/01 (with leading zero for numbers < 10)
    return $day . $month . '/' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
}

// Function to get preview of next parcel ID (for display only)
function getNextParcelIdPreview($pdo) {
    $day = date('d'); // Day of month
    $month = strtoupper(date('M')); // Month abbreviation in uppercase
    
    // Get all parcel IDs for today with different possible formats
    $patterns = [
        $day . ' ' . $month . '/%',      // "19 JUN/%"
        $day . ' ' . ucfirst(strtolower($month)) . '/%',  // "19 Jun/%"
        $day . $month . '/%',            // "19JUN/%"
        $day . ucfirst(strtolower($month)) . '/%'         // "19Jun/%"
    ];
    
    $allParcels = [];
    foreach ($patterns as $pattern) {
        $stmt = $pdo->prepare("SELECT Parcel_id FROM Parcel_info WHERE Parcel_id LIKE :pattern");
        $stmt->execute([':pattern' => $pattern]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
    return $day . $month . '/' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token validation
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("Invalid CSRF token.");
    }

    $phone = $_POST['PhoneNum'];
    $parcel_type = $_POST['Parcel_type'];
    $owner = $_POST['Parcel_owner'];
    
    // Generate auto-incrementing parcel ID
    $parcel_id = generateParcelId($pdo);

    try {
        $stmt = $pdo->prepare("INSERT INTO Parcel_info (PhoneNum, Parcel_type, Parcel_owner, Parcel_id, Date_arrived, Date_received, Parcel_image, Status)  VALUES (:phone, :type, :owner, :parcel_id, NOW(), NULL, :image, 0)");
        $stmt->execute([
            ':phone' => $phone,
            ':type' => $parcel_type,
            ':owner' => $owner,
            ':parcel_id' => $parcel_id
        ]);
        echo "<script>alert('Parcel added successfully!');</script>";
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            $_SESSION['success'] = "Parcel ID already exists.";
        } else {
            $_SESSION['success'] = "Error adding parcel: " . $e->getMessage();
        }
    }

    // Redirect to AdminView after processing
    header("Location: AdminView.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/x-icon" href="../resources/favicon.ico" />
  <link rel="stylesheet" href="../css/NewParcel.css" />
  <link rel="stylesheet" href="../css/style.css" />
  <title>Parcel Serumpun - Add Parcel</title>
  <style>
    /* --- Modern Card Container --- */
    .parcel-card {
      background: #fff;
      box-shadow: 0 4px 24px 0 rgba(73,91,191,0.10), 0 1.5px 6px 0 rgba(73,91,191,0.08);
      border-radius: 18px;
      padding: 36px 32px 28px 32px;
      max-width: 420px;
      width: 100%;
      margin: 40px auto 0 auto;
      display: flex;
      flex-direction: column;
      align-items: stretch;
      transition: box-shadow 0.2s;
    }
    .parcel-card:hover {
      box-shadow: 0 8px 32px 0 rgba(73,91,191,0.18), 0 3px 12px 0 rgba(73,91,191,0.12);
    }
    /* --- Enhanced Info Box --- */
    .parcel-info-box {
      background: #f5f7ff;
      border: 1.5px solid #dbe3ff;
      border-radius: 10px;
      padding: 16px 12px;
      margin: 18px 0 0 0;
      text-align: center;
      color: #495bbf;
      font-size: 1.08rem;
      font-family: 'Exo-Bold', sans-serif;
      box-shadow: 0 1px 4px 0 rgba(73,91,191,0.06);
    }
    /* --- Field and Button Tweaks --- */
    .parcel-form label {
      font-size: 1.08rem;
      color: #495bbf;
      font-weight: 600;
      margin-bottom: 6px;
      margin-top: 18px;
    }
    .parcel-form input,
    .parcel-form select {
      background: #f7f8fa;
      border: 1.5px solid #dbe3ff;
      font-size: 1rem;
      color: #222;
      margin-bottom: 8px;
      transition: border 0.2s;
    }
    .parcel-form input:focus,
    .parcel-form select:focus {
      border: 1.5px solid #495bbf;
      outline: none;
      background: #fff;
    }
    .add-button {
      background: linear-gradient(90deg, #495bbf 60%, #6e7ff3 100%);
      color: #fff;
      font-size: 1.1rem;
      font-weight: 700;
      letter-spacing: 0.5px;
      border: none;
      border-radius: 8px;
      margin-top: 28px;
      padding: 12px 0;
      box-shadow: 0 2px 8px 0 rgba(73,91,191,0.10);
      transition: background 0.2s, box-shadow 0.2s;
    }
    .add-button:hover {
      background: linear-gradient(90deg, #6e7ff3 0%, #495bbf 100%);
      box-shadow: 0 4px 16px 0 rgba(73,91,191,0.16);
    }
    @media (max-width: 600px) {
      .parcel-card {
        padding: 18px 8px 18px 8px;
        max-width: 98vw;
      }
      .title {
        font-size: 1.3rem;
        padding-right: 0;
      }
    }
  </style>
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
  </div>

  <div class="row">
    <a href="AdminView.php">
      <img class="back" src="../resources/Login/arrow-back0.svg" />
    </a>
    <p class="title">ADD NEW PARCEL</p>
  </div>

  <div class="enter-new-parcel">
    <div class="parcel-card">
      <form class="parcel-form" method="POST" action="">
        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <label for="parcel-type">Parcel Type</label>
        <div class="textfield">
          <select id="parcel-type" name="Parcel_type" required>
            <option value="">Select Parcel Type</option>
            <option value="KOTAK">KOTAK</option>
            <option value="HITAM">HITAM</option>
            <option value="PUTIH">PUTIH</option>
            <option value="KELABU">KELABU</option>
            <option value="OTHERS">OTHERS</option>
          </select>
        </div>

        <label for="phone">Phone Number</label>
        <div class="textfield">
          <input
            type="tel"
            id="phone"
            name="PhoneNum"
            placeholder="Enter phone number"
            required
          />
        </div>

        <label for="name">Owner's Name</label>
        <div class="textfield">
          <input
            type="text"
            id="name"
            name="Parcel_owner"
            placeholder="Enter receiver's name"
            required
          />
        </div>

        <div class="parcel-info-box">
          Current Date: <?php echo date('Y-m-d H:i:s'); ?><br>
          Parcel ID will be automatically generated<br>
          <strong>Next ID: <?php echo getNextParcelIdPreview($pdo); ?></strong>
        </div>

        <button type="submit" class="add-button">Add</button>
      </form>
    </div>
  </div>

  <footer class="trademark">
    Trademark ® 2025 Parcel Serumpun. All Rights Reserved
  </footer>
</body>
</html>
