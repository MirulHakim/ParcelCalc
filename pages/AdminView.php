<?php
session_start();

// Check login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: Login.php");
    exit();
}

require_once "pdo.php"; // Include DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['PhoneNum'];
    $parcel_type = $_POST['Parcel_type'];
    $owner = $_POST['Parcel_owner'];
    $parcel_id = $_POST['Parcel_Id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO Parcel_info (PhoneNum, Parcel_type, Parcel_owner, Parcel_id)  VALUES (:phone, :type, :owner, :parcel_id)");
        $stmt->execute([
            ':phone' => $phone,
            ':type' => $parcel_type,
            ':owner' => $owner,
            ':parcel_id' => $parcel_id
        ]);
        echo "<script>alert('Parcel added successfully');</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $delete_id = $_POST['delete_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM Parcel_info WHERE Parcel_id = :parcel_id");
        $stmt->execute([':parcel_id' => $delete_id]);
        echo "<script>alert('Parcel deleted successfully');</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Delete Error: " . $e->getMessage() . "');</script>";
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
    <a href="#">Create PDF</a>
  </div>

  <div class="main-content">
    <h2>Add New Parcel</h2>
    <form method="POST" action="">
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
      <input type="text" id="parcelID" name="Parcel_Id" placeholder="Enter Parcel ID" required style="width: 88.2%;" /><br>

      <button type="submit" style="width: 90%; background: #495bbf;">Add to list</button>
    </form>
    <?php

// Fetch all parcels
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
}
?>


    <h3>Parcel Info</h3>
    <input type="text" placeholder="Enter parcel ID" style="width: 88.3%;" />
    <p>Parcel ID Searching</p>

    <div class="parcel-info">
      <div class="parcel-detail"><span>Owner's Name:</span><span>-</span></div>
      <div class="parcel-detail"><span>Arrive Date:</span><span>24 June</span></div>
      <div class="parcel-detail"><span>Parcel ID:</span><span>246-05</span></div>
      <div class="parcel-detail"><span>Phone Number:</span><span>010-676 9035</span></div>
      <div class="parcel-detail"><span>Price:</span><span>RM 2.50</span></div>
      <div class="parcel-detail"><span>Status:</span><span>Not Claimed</span></div>
      <div class="button-group">
        <button class="button edit-btn">✏️ Edit</button>
        <button class="button delete-btn">🗑 Delete</button>
        <button class="button claim-btn">✅ Claim</button>
      </div>
    </div>
  </div>
  <footer>Trademark © 2025 Parcel Serumpun. All Rights Reserved</footer>
</body>
</html>