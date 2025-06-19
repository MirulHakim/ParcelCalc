<?php

// Database connection
$host = 'localhost';
$db = 'parcelsystem';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$stmt = $pdo->prepare("SELECT * FROM parcel WHERE Parcel_id = ?");
$stmt->execute([$parcelId]);
$parcel = $stmt->fetch();

if (!$parcel) {
    die("Parcel not found.");
}
?>

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
    <div class="header">
      <div class="row" style="gap: 0px">
        <div class="box blue" style="position: relative; z-index: 0"></div>
        <div class="box trapezium" style="position: relative; z-index: 1"></div>
        <div class="row logos">
          <img class="logo" src="../resources/Header/image-10.png" />
          <div class="x">X</div>
          <img class="logo" src="../resources/Header/logo-k-14-10.png" />
        </div>
        <a href="Login.php">
          <button class="login-button">LOGIN</button>
        </a>
      </div>
    </div>

    <div class="back-wrapper">
      <a href="Homepage.php">
        <img class="back" src="../resources/Login/arrow-back0.svg" />
      </a>
    </div>

    <div class="content">
      <div class="column">
        <p class="section-title">Parcel Info</p>
        <div class="container">
          <div class="image">
            <img src="get_image.php?Parcel_id=19JUN/09" width="300">
          </div>
          <div class="details">
              <p class="title"><?php echo htmlspecialchars($parcel['Owner_name']); ?></p>
          <div class="info">
              <span>Arrive date -</span>
              <span><?php echo htmlspecialchars($parcel['Arrive_date']); ?></span>
          </div>
          <div class="info">
              <span>Parcel ID -</span>
              <span><?php echo htmlspecialchars($parcel['Parcel_id']); ?></span>
          </div>
          <div class="info">
              <span>Phone number -</span>
              <span><?php echo htmlspecialchars($parcel['Phone_number']); ?></span>
          </div>
          <div class="info">
              <span>Price -</span>
              <span>RM <?php echo number_format($parcel['Price'], 2); ?></span>
          </div>
          <div class="info">
              <span>Status -</span>
              <span><?php echo $parcel['Status'] == 0 ? 'Not Claimed' : 'Claimed'; ?></span>
          </div>
      </div>
    </div>
  </div>
</div>

      <div class="column">
        <div class="row"></div>
        <div></div>
      </div>
    </div>

    <div class="trademark" style="margin-top: 100px">
      Trademark ® 2025 Parcel Serumpun. All Rights Reserved
    </div>
  </body>
</html>
