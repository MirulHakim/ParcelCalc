function checkDate() {
    const input = document.getElementById('date').value;
    const selectedDate = new Date(input);
    const today = new Date();

    // Clear time from today's date for accurate comparison
    today.setHours(0, 0, 0, 0);

    if (!input) {
      document.getElementById('result').textContent = "Please select a date.";
    } else if (selectedDate < today) {
      document.getElementById('result').textContent = "That date is in the past.";
    } else if (selectedDate > today) {
      document.getElementById('result').textContent = "That date is in the future.";
    } else {
      document.getElementById('result').textContent = "That's today!";
    }
  }