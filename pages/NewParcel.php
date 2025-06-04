<?php
require_once "pdo.php"; // make sure pdo.php is in the same folder or update the path

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $parcel_type = $_POST['parcelType'];
    $phone = $_POST['phone'];
    $owner = $_POST['name'];

    try {
        $stmt = $pdo->prepare("INSERT INTO parcel_test (PhoneNum, Parcel_type, Parcel_owner) VALUES (:phone, :type, :owner)");
        $stmt->execute([
            ':phone' => $phone,
            ':type' => $parcel_type,
            ':owner' => $owner
        ]);
        echo "<script>alert('Parcel added successfully!');</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
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
    <a onclick="history.back()">
      <img class="back" src="../resources/Login/arrow-back0.svg" />
    </a>
    <p class="title">ADD NEW PARCEL</p>
  </div>

  <div class="enter-new-parcel">
    <form class="parcel-form" method="POST" action="">
      <label for="parcel-type">Parcel Type</label>
      <div class="textfield">
        <select id="parcel-type" name="parcelType" required>
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
          name="phone"
          placeholder="Enter phone number"
          required
        />
      </div>

      <label for="name">Owner's Name</label>
      <div class="textfield">
        <input
          type="text"
          id="name"
          name="name"
          placeholder="Enter receiver's name"
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