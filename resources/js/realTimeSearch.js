document.addEventListener('DOMContentLoaded', function () {
    const campusFilter = document.getElementById('campusFilter');
    const searchInput = document.getElementById('searchInput');
    const contactsResults = document.getElementById('contactsResults');

    function fetchContacts() {
        const search = searchInput.value;
        const campusId = campusFilter.value;

        // Construct the URL with query parameters for search and campus
        const url = `/app-management?search=${encodeURIComponent(search)}&campus_id=${encodeURIComponent(campusId)}&section=contacts`;

        // Make an AJAX request to fetch the filtered contacts
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest' // Mark as an AJAX request
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text(); // Get the response as text (HTML)
        })
        .then(data => {
            // Update the contactsResults div with the new HTML content
            contactsResults.innerHTML = data;
        })
        .catch(error => console.error('Error fetching contacts:', error));
    }

    // Trigger the search when the campus filter changes
    campusFilter.addEventListener('change', fetchContacts);

    // Trigger the search on typing in the search input
    searchInput.addEventListener('input', fetchContacts);
});
