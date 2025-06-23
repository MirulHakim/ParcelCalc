<?php
session_start();
if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
  header("Location: Login.php");
  exit();
}

// Database connection
require_once "../controllers/pdo.php";

$parcel = null;
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
  $search_id = trim($_POST['name']);

  try {
    $stmt = $pdo->prepare("SELECT * FROM Parcel_info WHERE Parcel_id = :parcel_id");
    $stmt->execute([':parcel_id' => $search_id]);
    $parcel = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$parcel) {
      $error = "No parcel found with ID: " . htmlspecialchars($search_id);
    }
  } catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Parcel Details</title>
    <link rel="icon" type="image/x-icon" href="../resources/favicon.ico" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/ParcelView.css" />
  <link rel="stylesheet" href="../css/mousetrailer.css" />
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
      <a href="../controllers/logout.php">
        <button class="login-button">LOGOUT</button>
        </a>
      </div>
    <div id="clock"></div>
    </div>

    <div class="row">
    <a href="Homepage.php"><img class="back" src="../resources/Login/arrow-back0.svg" /></a>
    <form action="" method="post" class="search-form">
      <input class="search" type="text" id="name" name="name" placeholder="Enter your parcel ID"
        value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" />
      <button type="submit" style="height: 60px;">Search</button>
      </form>
    </div>

    <div class="content">
      <div class="column">
        <p class="section-title">Parcel Info</p>

      <?php if ($error): ?>
        <div class="container">
          <div class="details">
            <p style="color: red; text-align: center; margin: 20px 0;"><?php echo $error; ?></p>
          </div>
        </div>
      <?php elseif ($parcel): ?>
        <div class="container">
          <?php if (!empty($parcel['Parcel_image'])): ?>
            <div class="image-container">
              <img src="../controllers/get_image.php?Parcel_id=<?php echo urlencode($parcel['Parcel_id']); ?>"
                alt="Parcel Image">
            </div>
          <?php else: ?>
            <div class="image-placeholder">
              <p>No Image Available</p>
            </div>
          <?php endif; ?>

          <div class="details">
            <p class="title"><?php echo htmlspecialchars($parcel['Parcel_owner']); ?></p>

              <div class="info">
              <span>Arrival date -</span>
              <span><?php echo $parcel['Date_arrived'] ? date('d F Y', strtotime($parcel['Date_arrived'])) : 'Not Available'; ?></span>
              </div>

              <div class="info">
                  <span>Parcel ID -</span>
              <span><?php echo htmlspecialchars($parcel['Parcel_id']); ?></span>
              </div>

              <div class="info">
                  <span>Phone number -</span>
              <span><?php echo htmlspecialchars($parcel['PhoneNum']); ?></span>
            </div>

            <div class="info">
              <span>Parcel Type -</span>
              <span><?php echo htmlspecialchars($parcel['Parcel_type']); ?></span>
              </div>

            <?php
            // Calculate price based on arrival date
            $price = 1.00;
            if ($parcel['Date_arrived'] && $parcel['Date_arrived'] != '0000-00-00') {
              $arrived = new DateTime($parcel['Date_arrived']);
              $today = new DateTime();
              $interval = $today->diff($arrived);
              $days = $interval->days;

              if ($today > $arrived && $days > 1) {
                $price += ($days - 1) * 0.50;
              }
            }
            ?>

              <div class="info">
                  <span>Price -</span>
              <span>RM <?php echo number_format($price, 2); ?></span>
              </div>

              <div class="info">
                  <span>Status -</span>
              <span style="color: <?php echo $parcel['Status'] == 1 ? 'green' : 'red'; ?>;">
                <?php echo $parcel['Status'] == 1 ? 'Claimed' : 'Not Claimed'; ?>
              </span>
              </div>
          </div>
      </div>
      <?php else: ?>
        <div class="container">
          <div class="details">
            <p style="color: #495bbf; text-align: center; margin: 20px 0;">Enter a parcel ID to search for parcel
              information</p>
          </div>
        </div>
      <?php endif; ?>
      </div>

      <div class="column">
        <div class="row"></div>
        <div></div>
      </div>
    </div>

  <div class="trademark">
      Trademark ® 2025 Parcel Serumpun. All Rights Reserved
    </div>
  </body>
<script src="../js/clock.js" defer></script>
<script src="../js/mousetrailer.js" defer></script>

</html>