<?php
session_start();

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$host = 'localhost';
$db   = 'parcelsystem';
$user = 'root';
$pass = '';
$error = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // CSRF check
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Invalid CSRF token.");
        }

        $staff_id = $_POST['staff_id'] ?? '';
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM staff WHERE Staff_id = ?");
        $stmt->execute([$staff_id]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['Password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['staff_id'] = $staff_id;
            header("Location: AdminView.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/x-icon" href="../resources/favicon.ico" />
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/Login.css" />
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
    </div>

    <a href="Homepage.php"><img class="back" src="../resources/Login/arrow-back0.svg" /></a>
    <div class="content">
      <div
        class="row"
        style="
          display: flex;
          justify-content: center;
          align-items: center;
        "
      >
        <img
          class="logo"
          style="aspect-ratio: 95.31/120.46"
          src="../resources/Header/image-10.png"
        />
        <div class="x" style="margin: auto 0px">X</div>
        <img
          class="logo"
          style="aspect-ratio: 95.31/126.46"
          src="../resources/Header/logo-k-14-10.png"
        />
      </div>

      <p class="parcel">Parcel Serumpun</p>
      <p class="parcel mid">Admin Login</p>
      <p class="parcel small">
        Enter your username and password to continue in admin view
      </p>

      <?php if (!empty($error)): ?>
        <p style="color: red; text-align: center;"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <form action="" method="post">
        <div class="login-wrap">
          <input
            class="login"
            type="text"
            id="staff_id"
            name="staff_id"
            placeholder="Username"
          /><br />
          <input
            class="login"
            type="password"
            id="password"
            name="password"
            placeholder="Password"
          /><br />

          <input class="submit" type="submit" value="Login" />
        </div>
        <br />
      </form>

      <div class="trademark">
        Trademark ® 2025 Parcel Serumpun. All Rights Reserved
      </div>
    </div>
  </body>
</html>