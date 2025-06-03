<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/x-icon" href="../resources/favicon.ico" />
    <!-- <link rel="stylesheet" href="../css/NewParcel.css" /> -->
    <!-- <link rel="stylesheet" href="../css/style.css" /> -->
    <title>Parcel Serumpun - Add Parcel</title>
</head>
<body>
  <div class="enter-new-parcel">
    <div class="header">
      <div class="rectangle-1"></div>
      <div class="rectangle-2"></div>
      <img class="polygon-1" src="polygon-10.svg" />
      <div class="logos">
        <img class="image-1" src="resources/image-10.png" />
        <img class="logo-k-14-1" src="resources/logo-k-14-10.png" />
        <div class="x">X</div>
      </div>
    </div>
    <form class="parcel-form">
      <h2 class="form-heading">Add New Parcel</h2>
  
      <label for="parcel-type">Parcel Type</label>
      <div class="textfield">
        <div class="rectangle-62"></div>
        <select id="parcel-type" name="parcelType" required>
          <option value="">Select Parcel Type</option>
          <option value="kotak">KOTAK</option>
          <option value="hitam">HITAM</option>
          <option value="putih">PUTIH</option>
          <option value="kelabu">KELABU</option>
          <option value="others">OTHERS</option>
        </select>
      </div>

      <label for="phone">Phone Number</label>
      <div class="textfield">
        <div class="rectangle-62"></div>
        <input type="tel" id="phone" name="phone" placeholder="Enter phone number" required />
      </div>

      <label for="name">Owner's Name</label>
      <div class="textfield">
        <div class="rectangle-62"></div>
        <input type="text" id="name" name="name" placeholder="Enter receiver's name" required />
      </div>

      <button type="submit" class="add-button">Add</button>
    </form>
    <div class="trademark">
      Trademark ® 2025 Parcel Serumpun. All Rights Reserved
    </div>
  </div>
</body>
</html>
