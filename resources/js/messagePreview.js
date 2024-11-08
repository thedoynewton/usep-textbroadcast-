// Function to format and display the current timestamp
function updateTimestamp() {
    const timestampElement = document.getElementById('message-timestamp');
    const now = new Date();
    const formattedTime = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    timestampElement.textContent = formattedTime;
}

// Call the function when the modal opens or message is previewed
updateTimestamp();
