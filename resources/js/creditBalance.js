// creditBalance.js

// Function to fetch and update the displayed credit balance
function updateCreditBalance() {
    fetch('/credit-balance/get') // Update this route as per the new setup
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const balanceElement = document.getElementById('creditBalanceDisplay');
            if (balanceElement) {
                balanceElement.innerText = data.creditBalance.toLocaleString();
            }
        })
        .catch(error => {
            console.error('Error fetching credit balance:', error);
        });
}

// Initialize the real-time balance update
document.addEventListener('DOMContentLoaded', () => {
    updateCreditBalance(); // Update balance on page load
    setInterval(updateCreditBalance, 60000); // Refresh every 60 seconds
});
