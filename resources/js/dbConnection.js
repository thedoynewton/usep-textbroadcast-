document.addEventListener('DOMContentLoaded', function () {
    const obreroCard = document.getElementById('obreroCard');
    const obreroModal = document.getElementById('obreroModal');
    const closeObreroModal = document.getElementById('closeObreroModal');

    // Show the modal when the card is clicked
    obreroCard.addEventListener('click', function () {
        obreroModal.classList.remove('hidden');
    });

    // Hide the modal when the close button is clicked
    closeObreroModal.addEventListener('click', function () {
        obreroModal.classList.add('hidden');
    });

    // Optional: Hide modal when clicking outside of the modal content
    window.addEventListener('click', function (e) {
        if (e.target === obreroModal) {
            obreroModal.classList.add('hidden');
        }
    });
});
