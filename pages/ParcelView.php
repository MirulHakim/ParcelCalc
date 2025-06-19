<?php
require_once "pdo.php";
$parcel = null;
$price = 0.00;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['parcel_id'])) {
    $search_id = $_POST['parcel_id'];

    $stmt = $pdo->prepare("SELECT * FROM Parcel_info WHERE Parcel_id = :id");
    $stmt->execute([':id' => $search_id]);
    $parcel = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($parcel) {
        $arrivedDate = $parcel['Date_arrived'];
        $arrived = new DateTime($arrivedDate);
        $today = new DateTime();
        $days = $today->diff($arrived)->days;

        $price = 1.00;
        if ($today > $arrived && $days > 1) {
            $price += ($days - 1) * 0.50;
        }
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
</head>

<body>
  <div class="header">
    <div class="row" style="gap: 0px">
      <div class="box blue"></div>
      <div class="box trapezium"></div>
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

  <div class="center-row">
    <form method="POST" action="">
      <input class="search" type="text" name="parcel_id" placeholder="Enter your Parcel ID" required />
      <button type="submit">Search</button>
    </form>
  </div>

  <?php if ($parcel): ?>
  <div class="content">
    <div class="column">
      <p class="section-title">Parcel Info</p>
      <div class="container">
        <div class="image">
          <img src="get_image.php?Parcel_id=<?= urlencode($parcel['Parcel_id']) ?>" width="300" />
        </div>
        <div class="details">
          <p class="title"><?= htmlspecialchars($parcel['Parcel_owner']) ?></p>
          <div class="info">
            <span>Arrive date -</span>
            <span><?= htmlspecialchars($parcel['Date_arrived']) ?></span>
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
            <span>RM <?= number_format($price, 2) ?></span>
          </div>
          <div class="info">
            <span>Status -</span>
            <span><?= $parcel['Status'] == 0 ? 'Not Claimed' : 'Claimed' ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
  <div class="center-row">
    <p style="color:red;">❌ No parcel found with that ID.</p>
  </div>
  <?php endif; ?>

  <div class="trademark" style="margin-top: 100px">
    Trademark ® 2025 Parcel Serumpun. All Rights Reserved
  </div>
</body>
</html>
