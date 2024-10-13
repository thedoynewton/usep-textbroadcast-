// resources/js/sendMessageToggle.js

document.addEventListener('DOMContentLoaded', function () {
    const sendNowRadio = document.getElementById('send_now');
    const sendLaterRadio = document.getElementById('send_later');
    const sendDateTimeInput = document.getElementById('send_date').parentElement;

    // Initially hide the date input if "Send Now" is checked
    if (sendNowRadio.checked) {
        sendDateTimeInput.style.display = 'none';
    }

    // Show/hide the date input based on the selected radio option
    sendNowRadio.addEventListener('change', function () {
        if (sendNowRadio.checked) {
            sendDateTimeInput.style.display = 'none';
        }
    });

    sendLaterRadio.addEventListener('change', function () {
        if (sendLaterRadio.checked) {
            sendDateTimeInput.style.display = 'block';
        }
    });
});
