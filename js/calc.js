function checkDate() {
  const inputElement = document.getElementById("date");
  const result = document.getElementById("result");

  if (!inputElement || !result) return;

  const input = inputElement.value;
  if (!input) {
    result.textContent = "Sila pilih tarikh dahulu.";
    return;
  }

  const selectedDate = new Date(input);
  const today = new Date();

  selectedDate.setHours(0, 0, 0, 0);
  today.setHours(0, 0, 0, 0);

  const diffTime = selectedDate - today;
  const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));

  let price = 1.0;

  if (diffDays < 0) {
    const diffMonths = Math.floor(Math.abs(diffDays) / 30);

    if (diffMonths > 0) {
      price += diffMonths * 0.5;
      price = Math.min(price, 5.0);
    } else {
      if (diffDays < -1) {
        price += (Math.abs(diffDays) - 1) * 0.5;
        price = Math.min(price, 5.0);
      }
    }

    result.textContent = `RM${price.toFixed(2)}`;
  } else if (diffDays > 0) {
    result.textContent = `This parcel is not in the system.`;
  } else {
    result.textContent = `RM1.00`;
  }
}
