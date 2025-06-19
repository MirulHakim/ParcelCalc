<?php
// viewParcel.php

$host = 'localhost';
$db = 'parcelsystem';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form input
$parcelInfo = null;
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['name'])) {
    $parcel_id = $_POST['name'];
    $stmt = $pdo->prepare("SELECT * FROM Parcel_info WHERE Parcel_id = :parcel_id");
    $stmt->execute(['parcel_id' => $parcel_id]);
    $parcelInfo = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!-- HTML starts here -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>View Parcel Info</title>
  <link rel="icon" type="image/x-icon" href="../resources/favicon.ico" />
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../css/ParcelView.css" />
</head>
<body>
  <!-- header -->
  <div class="header">
    <!-- ... (same as before) ... -->
  </div>

  <!-- search form -->
  <div class="row">
    <a href="Homepage.php"><img class="back" src="../resources/Login/arrow-back0.svg"/></a>
    <form action="" method="post">
      <input class="search" type="text" id="name" name="name" placeholder="Enter your parcel ID" />
    </form>
  </div>

  <!-- content -->
  <div class="content">
    <div class="column">
      <p class="section-title">Parcel Info</p>
      <div class="container">
        <?php if ($parcelInfo): ?>
          <div class="image">
            <img src="get_image.php?Parcel_id=<?= urlencode($parcelInfo['Parcel_id']) ?>" width="300">
          </div>
          <div class="details">
              <p class="title">Owner’s name</p>
              <div class="info"><span>Arrive date -</span><span><?= htmlspecialchars($parcelInfo['Date_arrived']) ?></span></div>
              <div class="info"><span>Parcel ID -</span><span><?= htmlspecialchars($parcelInfo['Parcel_id']) ?></span></div>
              <div class="info"><span>Phone number -</span><span><?= htmlspecialchars($parcelInfo['PhoneNum']) ?></span></div>
              <div class="info"><span>Price -</span><span>RM <?= number_format($parcelInfo['Price'], 2) ?></span></div>
              <div class="info"><span>Status -</span><span><?= $parcelInfo['Status'] == 0 ? 'Not Claimed' : 'Claimed' ?></span></div>
          </div>
        <?php else: ?>
          <p style="text-align:center;">Enter your Parcel ID to view parcel details.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="trademark" style="margin-top: 100px">
    Trademark ® 2025 Parcel Serumpun. All Rights Reserved
  </div>
</body>
</html>
