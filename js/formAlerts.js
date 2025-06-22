// Form Alert Handler
// This script handles displaying success and error messages as JavaScript alerts
// Usage: Include this script and set window.successMsg and window.errorMsg variables

document.addEventListener("DOMContentLoaded", function () {
  // Check for success message
  if (typeof window.successMsg !== "undefined" && window.successMsg) {
    alert(window.successMsg);
  }

  // Check for error message
  if (typeof window.errorMsg !== "undefined" && window.errorMsg) {
    alert("Error: " + window.errorMsg);
  }
});
