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

    <div class="row">
      <a onclick="history.back()"
        ><img class="back" src="../resources/Login/arrow-back0.svg"
      /></a>
      <form action="" method="post">
        <input
          class="search"
          type="text"
          id="name"
          name="name"
          placeholder="Enter your parcel ID"
        />
      </form>
    </div>

    <div class="content">
      <div class="column">
        <p class="section-title">Parcel Info</p>
        <div class="container">
          <div class="image"></div>
          <div class="details">
              <p class="title">Owner’s name</p>
              <div class="info">
                  <span>Arrive date -</span>
                  <span>24 June</span>
              </div>
              <div class="info">
                  <span>Parcel ID -</span>
                  <span>24/6-05</span>
              </div>
              <div class="info">
                  <span>Phone number -</span>
                  <span>010-876 9035</span>
              </div>
              <div class="info">
                  <span>Price -</span>
                  <span>RM 2.50</span>
              </div>
              <div class="info">
                  <span>Status -</span>
                  <span>Not Claimed</span>
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
