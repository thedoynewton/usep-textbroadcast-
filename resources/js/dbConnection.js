document.addEventListener('DOMContentLoaded', function () {
    const obreroCard = document.getElementById('obreroCard');
    const obreroModal = document.getElementById('obreroModal');
    const closeObreroModal = document.getElementById('closeObreroModal');

    // Show the modal when the card is clicked, only if obreroCard exists
    if (obreroCard && obreroModal) {
        obreroCard.addEventListener('click', function () {
            obreroModal.classList.remove('hidden');
        });
    }

    // Hide the modal when the close button is clicked, only if closeObreroModal exists
    if (closeObreroModal && obreroModal) {
        closeObreroModal.addEventListener('click', function () {
            obreroModal.classList.add('hidden');
        });
    }

    // Optional: Hide modal when clicking outside of the modal content, only if obreroModal exists
    if (obreroModal) {
        window.addEventListener('click', function (e) {
            if (e.target === obreroModal) {
                obreroModal.classList.add('hidden');
            }
        });
    }
});
