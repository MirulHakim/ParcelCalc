function updateClock() {
  const now = new Date();
  const clockElement = document.getElementById("clock");
  if (clockElement) {
    // Options for formatting date and time
    const dateOptions = {
      weekday: "long",
      year: "numeric",
      month: "long",
      day: "numeric",
    };
    const timeOptions = {
      hour: "2-digit",
      minute: "2-digit",
      second: "2-digit",
      hour12: true,
    };

    const dateString = now.toLocaleDateString("en-US", dateOptions);
    const timeString = now.toLocaleTimeString("en-US", timeOptions);

    clockElement.innerHTML = `${dateString} | ${timeString}`;
  }
}

// Update the clock every second
setInterval(updateClock, 1000);

// Initial call to display the clock immediately
updateClock();
