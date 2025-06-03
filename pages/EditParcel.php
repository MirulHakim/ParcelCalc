<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../resources/favicon.ico" />
    <link rel="stylesheet" href="../css/EditParcel.css" />
    <link rel="stylesheet" href="../css/style.css" />
    <title>Parcel Serumpun - Edit Parcel</title>
</head>
<body>
    <!-- Header logo -->
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
    <!-- back button & title -->
    <div class="row">
      <a onclick="history.back()"
        ><img class="back" src="../resources/Login/arrow-back0.svg"
      /></a>
      <p class="title">EDIT/DELETE PARCEL INFO</p>
    </div>
    <!-- Searchbar Parcel ID -->
    <form action="" method="post">
        <input class="search" type="text" id="name" name="name" placeholder="Enter parcel ID">
    </form>
    <!-- Parcel Detail -->
    <div class="edit-parcel-container">
  <form class="edit-parcel-form" method="POST" action="update_parcel.php">
    <input type="hidden" name="id" value="<?= $parcel['id'] ?>" />

    <div class="form-grid">
      <div class="form-group">
        <label>Owner's Name</label>
        <input type="text" value="<?= $parcel['owner_name'] ?>" disabled />
        <input type="text" name="new_owner_name" placeholder="New Owner's name" />
      </div>

      <div class="form-group">
          <label>Parcel Status</label>
          <select>
            <option value="">Select Parcel </option>
            <option value=False>Not claim</option>
            <option value=True>Claimed</option>
          </select>
        </div>

      <div class="form-group">
        <label>Parcel Type</label>
        <select disabled>
          <option><?= $parcel['parcel_type'] ?></option>
        </select>
        <select name="new_type">
          <option value="">Select new type</option>
          <option value="kotak">KOTAK</option>
          <option value="putih">PUTIH</option>
          <option value="hitam">HITAM</option>
          <option value="kelabu">KELABU</option>
          <option value="others">OTHERS</option>
        </select>
      </div>

      <div class="form-group">
        <label>Owner's Contact Info</label>
        <input type="text" value="<?= $parcel['contact_info'] ?>" disabled />
        <input type="text" name="new_contact" placeholder="New contact info" />
      </div>
    </div>

    <button type="submit" class="btn confirm">Confirm</button>
    <form method="POST" action="delete_parcel.php" onsubmit="return confirm('Are you sure you want to delete this parcel?');">
  <input type="hidden" name="id" value="<?= $parcel['id'] ?>" />
  <button type="submit" class="btn delete">Delete</button>
  </form>
</div>

</body>
</html>