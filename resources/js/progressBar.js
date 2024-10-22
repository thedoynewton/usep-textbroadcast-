document.addEventListener('DOMContentLoaded', function () {
    const messageForm = document.getElementById('message-form');
    const progressBarContainer = document.getElementById('progress-bar');
    const progressBar = document.getElementById('progress');

    messageForm.addEventListener('submit', function (event) {
        const sendNowRadio = document.getElementById('send_now');
        
        // Check if "Send Now" is selected
        if (sendNowRadio.checked) {
            event.preventDefault(); // Prevent the form from submitting immediately

            // Show the progress bar
            progressBarContainer.classList.remove('hidden');

            // Simulate progress
            let progress = 0;
            const interval = setInterval(function () {
                progress += 10;
                progressBar.style.width = progress + '%';

                // If progress reaches 100%, submit the form
                if (progress >= 100) {
                    clearInterval(interval);
                    messageForm.submit(); // Submit the form when progress reaches 100%
                }
            }, 300); // Adjust the time interval for the speed of the progress bar
        }
    });
});
