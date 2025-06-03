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

    <a onclick="history.back()"><img class="back" src="../resources/Login/arrow-back0.svg" /></a>
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

      <form action="AdminView.php" method="post">
        <div class="login-wrap">
          <input
            class="login"
            type="text"
            id="name"
            name="name"
            placeholder="Username"
          /><br />
          <input
            class="login"
            type="text"
            id="name"
            name="name"
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

<?php
session_start();

$host = 'localhost';
$db   = 'parcelsystem';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM staff WHERE username = ? AND password = ?");
        $stmt->execute([$username, $password]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['admin_logged_in'] = true;
            header("Location: AdminView.php"); // replace with your real page
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

