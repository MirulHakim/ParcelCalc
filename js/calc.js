function checkDate() {
    const input = document.getElementById('date');
    const result = document.getElementById('result');

    if (!input || !result) {
        console.error("❌ Input or result element not found!");
        return;
    }

    const inputValue = input.value;
    const arrivedDate = new Date(inputValue);
    const today = new Date();

    // Normalize to ignore time portion
    arrivedDate.setHours(0, 0, 0, 0);
    today.setHours(0, 0, 0, 0);

    const timeDiff = today.getTime() - arrivedDate.getTime();
    const dayDiff = Math.max(0, Math.floor(timeDiff / (1000 * 3600 * 24)));

    let price = 1.00;
    if (dayDiff > 1) {
        price += (dayDiff - 1) * 0.50;
    }

    result.innerText = "RM " + price.toFixed(2);
    console.log(`✅ Price for ${inputValue} is RM ${price.toFixed(2)} (${dayDiff} day(s) old)`);
}

window.addEventListener("DOMContentLoaded", checkDate);
