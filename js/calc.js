function checkDate() {
  const input = document.getElementById("date").value;
  const selectedDate = new Date(input);
  const today = new Date();

  selectedDate.setHours(0, 0, 0, 0);
  today.setHours(0, 0, 0, 0);

  const result = document.getElementById("result");

  if (!input) {
    result.textContent = "Sila pilih tarikh dahulu.";
    return;
  }

  const diffTime = selectedDate - today;
  const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));

  let price = 1.0;

  if (diffDays < 0) {
    const diffMonths = Math.floor(Math.abs(diffDays) / 30); // Approximate months (30 days each)

    if (diffMonths > 0) {
      // If the difference is more than a month
      price += diffMonths * 0.5;
      // Ensure price does not exceed $5.00
      price = Math.min(price, 5.0);
      result.textContent = `RM5.00`;
    } else {
      if (diffDays < -1) {
        // Less than a month
        price += (Math.abs(diffDays) - 1) * 0.5;
        price = Math.min(price, 5.0);
        result.textContent = `RM${price.toFixed(2)}`;
      } else {
        result.textContent = `RM1.00`;
      }
    }
  } else if (diffDays > 0) {
    result.textContent = `This parcel is not in the system. Please check again.`;
  } else {
    result.textContent = `RM1.00`;
  }
}
