function checkDate() {
    const input = document.getElementById('date').value;
    const selectedDate = new Date(input);
    const today = new Date();

    selectedDate.setHours(0, 0, 0, 0);
    today.setHours(0, 0, 0, 0);

    const result = document.getElementById('result');

    if (!input) {
        result.textContent = "Please select a date.";
        return;
    }

    const diffTime = selectedDate - today;
    const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));

    let price = 1.00;

    if (diffDays < 0) {
        const diffMonths = Math.floor(Math.abs(diffDays) / 30); // Approximate months (30 days each)

        if (diffMonths > 0) {
            // If the difference is more than a month
            price += diffMonths * 0.50;
            // Ensure price does not exceed $5.00
            price = Math.min(price, 5.00);
            result.textContent = `Parcel telah sampai ${diffMonths} bulan yang lalu. Harga: RM5.00`;
        } else {
            // Less than a month
            price += Math.abs(diffDays) * 0.50;
            price = Math.min(price, 5.00);
            result.textContent = `Parcel telah sampai ${Math.abs(diffDays)} hari yang lalu. Harga: RM${price.toFixed(2)}`;
        }
    } else if (diffDays > 0) {
        result.textContent = `Tarikh tersebut tidak diterima, sila isi semula.`;
    } else {
        result.textContent = `Parcel sampai harini. Harga: RM${price.toFixed(2)}`;
    }
}