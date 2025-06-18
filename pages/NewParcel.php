<?php
session_start();
require_once "pdo.php";

// Generate CSRF token if not already generated
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
    $parcel_id = $_POST['Parcel_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO Parcel_info (PhoneNum, Parcel_type, Parcel_owner, Parcel_id)  
                               VALUES (:phone, :type, :owner, :parcel_id)");
        $stmt->execute([
            ':phone' => $phone,
            ':type' => $parcel_type,
            ':owner' => $owner,
            ':parcel_id' => $parcel_id
        ]);
        $_SESSION['success'] = "Parcel added successfully.";
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
    <form class="parcel-form" method="POST" action="">
      <!-- CSRF Token -->
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

      <label for="parcel-type">Parcel Type</label>
      <div class="textfield">
        <select id="parcel-type" name="Parcel_type" required>
          <option value="">Select Parcel Type</option>
          <option value="kotak">KOTAK</option>
          <option value="hitam">HITAM</option>
          <option value="putih">PUTIH</option>
          <option value="kelabu">KELABU</option>
          <option value="others">OTHERS</option>
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

      <label for="parcelID">Parcel ID</label>
      <div class="textfield">
        <input
          type="text"
          id="parcelID"
          name="Parcel_id"
          placeholder="Enter parcel ID"
          required
        />
      </div>

      <button type="submit" class="add-button">Add</button>
    </form>
  </div>

  <footer class="trademark">
    Trademark ® 2025 Parcel Serumpun. All Rights Reserved
  </footer>
</body>
</html>
