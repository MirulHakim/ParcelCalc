<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create PDF</title>
    <link rel="icon" type="image/x-icon" href="../resources/favicon.ico" />
    <link rel="stylesheet" href="../css/PDF.css" />
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
        <a href="Login.php">
          <button class="login-button">LOGIN</button>
        </a>
      </div>
    </div>

    <div class="row">
      <a href="Homepage.php"
        ><img class="back" src="../resources/Login/arrow-back0.svg"
      /></a>
      <p class="title">CREATE PARCEL LIST PDF</p>
    </div>

    <div class="container">
      <div class="row">
        <div class="image"></div>
        <div class="column" style="margin-top: 15%">
          <p class="parcel">PARCEL - DD MONTH</p>
          <form action="" method="get">
            <input class="download" type="submit" value="DOWNLOAD" />
          </form>
        </div>
      </div>
    </div>

    <div class="trademark">
      Trademark ® 2025 Parcel Serumpun. All Rights Reserved
    </div>
  </body>
</html>
