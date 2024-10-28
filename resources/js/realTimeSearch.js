document.addEventListener('DOMContentLoaded', function () {
    const campusFilter = document.getElementById('campusFilter');
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter'); // New type filter
    const contactsResults = document.getElementById('contactsResults');

    function fetchContacts() {
        const search = searchInput.value;
        const campusId = campusFilter.value;
        const type = typeFilter.value; // Get selected type

        // Construct the URL with query parameters for search, campus, and type
        const url = `/app-management?search=${encodeURIComponent(search)}&campus_id=${encodeURIComponent(campusId)}&type=${encodeURIComponent(type)}&section=contacts`;

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

            // Highlight the search term after updating the HTML
            if (search) {
                highlightSearchTerm(contactsResults, search);
            }
        })
        .catch(error => console.error('Error fetching contacts:', error));
    }

    // Function to safely highlight search term within text nodes
    function highlightSearchTerm(element, term) {
        const regex = new RegExp(`(${term})`, 'gi'); // Case-insensitive matching

        function highlightTextNodes(node) {
            if (node.nodeType === Node.TEXT_NODE) {
                const matches = node.textContent.match(regex);
                if (matches) {
                    const wrapper = document.createDocumentFragment();
                    
                    // Loop through each part, wrapping matches
                    node.textContent.split(regex).forEach(part => {
                        if (part.toLowerCase() === term.toLowerCase()) {
                            const span = document.createElement('span');
                            span.style.backgroundColor = 'yellow';
                            span.textContent = part;
                            wrapper.appendChild(span);
                        } else {
                            wrapper.appendChild(document.createTextNode(part));
                        }
                    });

                    node.replaceWith(wrapper); // Replace original text node with highlighted fragment
                }
            } else {
                // Recursively process child nodes for highlighting
                node.childNodes.forEach(child => highlightTextNodes(child));
            }
        }

        highlightTextNodes(element);
    }

    // Event listeners for filters
    campusFilter.addEventListener('change', fetchContacts);
    searchInput.addEventListener('input', fetchContacts);
    typeFilter.addEventListener('change', fetchContacts); // Listen to type filter changes
});
