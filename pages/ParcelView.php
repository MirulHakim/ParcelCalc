<?php
require_once "pdo.php";
$parcel = null;
$errorMsg = '';
$priceText = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
  $searchId = trim($_POST['name']);
  $stmt = $pdo->prepare("SELECT * FROM Parcel_info WHERE Parcel_id = :id");
  $stmt->execute([':id' => $searchId]);
  $parcel = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$parcel) {
    $errorMsg = "No parcel found with ID: " . htmlspecialchars($searchId);
  }
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
  <script src="../js/calc.js"></script>
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

  <div class="center-row-relative">
    <a href="Homepage.php" class="back-absolute">
      <img class="back" src="../resources/Login/arrow-back0.svg" />
    </a>
    <form action="" method="post" class="center-search-form">
      <input class="search" type="text" id="name" name="name" placeholder="Enter your parcel ID" />
    </form>
  </div>

  <div class="content">
    <div class="column">
      <p class="section-title">Parcel Info</p>
      <?php if ($errorMsg): ?>
        <div class="success-message" style="background: #fbeaea; color: #b91c1c; border: 1.5px solid #f5c2c7;">
          <?= htmlspecialchars($errorMsg) ?>
        </div>
      <?php endif; ?>
      <?php if ($parcel): ?>
        <div class="container">
          <div class="image">
            <?php if (!empty($parcel['Parcel_image'])): ?>
              <img src="data:image/jpeg;base64,<?= base64_encode($parcel['Parcel_image']) ?>" alt="Parcel Image"
                style="max-width: 100%; max-height: 180px; border-radius: 12px;" />
            <?php else: ?>
              <img src="../resources/no_image.jpg" alt="No Image"
                style="max-width: 100%; max-height: 180px; border-radius: 12px; opacity: 0.5;" />
            <?php endif; ?>
          </div>
          <div class="details">
            <p class="title">Owner's name: <?= htmlspecialchars($parcel['Parcel_owner']) ?></p>
            <div class="info">
              <span>Arrive date -</span>
              <span id="arrive-date-text"><?= htmlspecialchars($parcel['Date_arrived']) ?></span>
            </div>
            <div class="info">
              <span>Parcel ID -</span>
              <span><?= htmlspecialchars($parcel['Parcel_id']) ?></span>
            </div>
            <div class="info">
              <span>Phone number -</span>
              <span><?= htmlspecialchars($parcel['PhoneNum']) ?></span>
            </div>
            <div class="info">
              <span>Price -</span>
              <span id="result">RM 2.50</span>
            </div>
            <div class="info">
              <span>Status -</span>
              <span><?= ($parcel['Status'] == 1 ? 'Claimed' : 'Not Claimed') ?></span>
            </div>
            <input type="hidden" id="date" value="<?= htmlspecialchars($parcel['Date_arrived']) ?>" />
            <script>
              // Only run if there is a parcel
              document.addEventListener('DOMContentLoaded', function () {
                if (document.getElementById('date')) {
                  checkDate();
                }
              });
            </script>
          </div>
        </div>
      <?php endif; ?>
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
