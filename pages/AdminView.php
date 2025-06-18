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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
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
    } elseif (isset($_POST['PhoneNum'], $_POST['Parcel_type'], $_POST['Parcel_owner'], $_POST['Parcel_id'])) {
        $phone = $_POST['PhoneNum'];
        $parcel_type = $_POST['Parcel_type'];
        $owner = $_POST['Parcel_owner'];
        $parcel_id = $_POST['Parcel_id'];

        try {
            // Check duplicate Parcel ID
            $check = $pdo->prepare("SELECT COUNT(*) FROM Parcel_info WHERE Parcel_id = :parcel_id");
            $check->execute([':parcel_id' => $parcel_id]);
            if ($check->fetchColumn() > 0) {
                $_SESSION['error'] = "❌ Parcel ID already exists!";
            } else {
                $stmt = $pdo->prepare("INSERT INTO Parcel_info (PhoneNum, Parcel_type, Parcel_owner, Parcel_id)  
                                       VALUES (:phone, :type, :owner, :parcel_id)");
                $stmt->execute([
                    ':phone' => $phone,
                    ':type' => $parcel_type,
                    ':owner' => $owner,
                    ':parcel_id' => $parcel_id
                ]);
                $_SESSION['success'] = "✅ Parcel added successfully!";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Insert Error: " . $e->getMessage();
        }
        header("Location: AdminView.php");
        exit();
    }
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
      if ($successMsg) echo "<p style='color: green; font-weight:bold;'>$successMsg</p>";
      if ($errorMsg) echo "<p style='color: red; font-weight:bold;'>$errorMsg</p>";
    ?>

    <h2>Add New Parcel</h2>
    <form method="POST" action="">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
      <label for="phone">Phone Number:</label><br>
      <input type="text" id="phone" name="PhoneNum" placeholder="Enter Phone Number" required style="width: 88.3%;" /><br>

      <label for="parcel-type">Parcel Type:</label><br>
      <select id="parcel-type" name="Parcel_type" required style="width: 90%;">
        <option value="kotak">KOTAK</option>
        <option value="hitam">HITAM</option>
        <option value="putih">PUTIH</option>
        <option value="kelabu">KELABU</option>
        <option value="others">OTHERS</option>
      </select><br>

      <label for="owner">Parcel Owner:</label><br>
      <input type="text" id="owner" name="Parcel_owner" placeholder="Enter Owner's Name" required style="width: 88.2%;" /><br>

      <label for="parcelID">Parcel ID:</label><br>
      <input type="text" id="parcelID" name="Parcel_id" placeholder="Enter Parcel ID" required style="width: 88.2%;" /><br>

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
        echo '<div class="parcel-detail"><span>Price:</span><span>RM 2.50</span></div>';
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