<?php
session_start();

require_once "../controllers/pdo.php";
$error = '';

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // CSRF token check
  if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("Invalid CSRF token.");
  }

  $user_id = $_POST['user_id'] ?? '';
  $password = trim($_POST['password'] ?? '');

  // Try to login as staff
  $stmt = $pdo->prepare("SELECT * FROM staff WHERE Staff_id = ?");
  $stmt->execute([$user_id]);
  $staff = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($staff && password_verify($password, $staff['Password'])) {
    // Successful staff login
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['staff_id'] = $user_id;
    session_regenerate_id(true);
    header("Location: AdminView.php");
    exit();
  }

  // Try to login as student
  $stmt = $pdo->prepare("SELECT * FROM student WHERE Student_id = ?");
  $stmt->execute([$user_id]);
  $student = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($student && isset($student['password'])) {
    if (password_verify($password, $student['password'])) {
      $_SESSION['student_logged_in'] = true;
      $_SESSION['student_id'] = $user_id;
      session_regenerate_id(true);
      header("Location: Homepage.php");
      exit();
    }
  }

  // Login failed
  $error = "Invalid username or password.";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/x-icon" href="../resources/favicon.ico" />
  <title>Login</title>
  <link rel="stylesheet" href="../css/Login.css" />
  <link rel="stylesheet" href="../css/style.css" />
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
    </div>
    <div id="clock"></div>
  </div>

  <a href="../"><img class="back" src="../resources/Login/arrow-back0.svg" /></a>
  <div class="content">
    <div class="row" style="
          display: flex;
          justify-content: center;
          align-items: center;
        ">
      <img class="logo" style="aspect-ratio: 95.31/120.46" src="../resources/Header/image-10.png" />
      <div class="x" style="margin: auto 0px">X</div>
      <img class="logo" style="aspect-ratio: 95.31/126.46" src="../resources/Header/logo-k-14-10.png" />
    </div>

    <p class="parcel">Parcel Serumpun</p>
    <p class="parcel mid">Login</p>
    <p class="parcel small">
      Enter your username and password to continue
    </p>

    <?php if (!empty($error)): ?>
      <div class="error-message"
        style="color: #b00020; background: #ffeaea; border: 1px solid #ffb3b3; padding: 10px; margin-bottom: 15px; text-align: center;">
        <?= htmlspecialchars($error) ?>
      </div>
      <p style="color: red; text-align: center;"> <?= htmlspecialchars($error) ?> </p>
    <?php endif; ?>

    <form action="" method="post">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
      <div class="login-wrap">
        <input class="login" type="text" id="user_id" name="user_id" placeholder="Staff or Student ID" /><br />
        <input class="login" type="password" id="password" name="password" placeholder="Password" /><br />
        <input class="submit" type="submit" value="Login" />
      </div>
      <br />
    </form>

    <div class="trademark">
      Trademark ® 2025 Parcel Serumpun. All Rights Reserved
    </div>
  </div>
</body>
<script src="../js/clock.js" defer></script>
<script src="../js/mousetrailer.js" defer></script>

</html>