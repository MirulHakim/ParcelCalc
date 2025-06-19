<?php
session_start();

require_once "pdo.php";
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['staff_id'] ?? '';
    $password = $_POST['password'] ?? '';

    // 1. Try staff login (plain password)
    $stmt = $pdo->prepare("SELECT * FROM staff WHERE Staff_id = ?");
    $stmt->execute([$user_id]);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($staff && isset($staff['Password']) && $password === $staff['Password']) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['staff_id'] = $user_id;
        header("Location: AdminView.php");
        exit();
    }

    // 2. Try student login (hashed password)
    $stmt = $pdo->prepare("SELECT * FROM student WHERE Student_id = ?");
    $stmt->execute([$user_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($student && isset($student['Password']) && password_verify($password, $student['Password'])) {
        $_SESSION['student_logged_in'] = true;
        $_SESSION['student_id'] = $user_id;
        header("Location: Homepage.php");
        exit();
    }

    // 3. If neither, show error
    $error = "Invalid username or password.";
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