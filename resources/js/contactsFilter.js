document.addEventListener('DOMContentLoaded', function () {
    const campusFilter = document.getElementById('campusFilter');
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter'); // New type filter
    const contactsResults = document.getElementById('contactsResults');

    const editModal = document.getElementById('editModal');
    const editForm = document.getElementById('editForm');
    const contactIdInput = document.getElementById('contactId');
    const contactNameInput = document.getElementById('contactName');
    const contactNumberInput = document.getElementById('contactNumber');
    let contactIdType = ''; // Track if ID is stud_id or emp_id

    function fetchContacts() {
        const search = searchInput.value;
        const campusId = campusFilter.value;
        const type = typeFilter.value;

        const url = `/app-management?search=${encodeURIComponent(search)}&campus_id=${encodeURIComponent(campusId)}&type=${encodeURIComponent(type)}&section=contacts`;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text();
        })
        .then(data => {
            contactsResults.innerHTML = data;

            if (search) {
                highlightSearchTerm(contactsResults, search);
            }

            initializeEditButtons();
        })
        .catch(error => console.error('Error fetching contacts:', error));
    }

    function highlightSearchTerm(element, term) {
        const regex = new RegExp(`(${term})`, 'gi');

        function highlightTextNodes(node) {
            if (node.nodeType === Node.TEXT_NODE) {
                const matches = node.textContent.match(regex);
                if (matches) {
                    const wrapper = document.createDocumentFragment();
                    
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

                    node.replaceWith(wrapper);
                }
            } else {
                node.childNodes.forEach(child => highlightTextNodes(child));
            }
        }

        highlightTextNodes(element);
    }

    function initializeEditButtons() {
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', () => {
                contactIdInput.value = button.getAttribute('data-id'); // Set stud_id or emp_id
                contactNameInput.value = button.getAttribute('data-name');
                contactNumberInput.value = button.getAttribute('data-contact');
                contactIdType = button.getAttribute('data-id-type'); // Track the ID type (stud_id or emp_id)

                const contactType = button.getAttribute('data-type');
                console.log("Contact Type:", contactType); // Log contact type
                console.log("Contact Number:", contactNumberInput.value); // Log contact number

                // Open the modal
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'editContactModal' }));
            });
        });
    }

    // AJAX submission of the edit form
    editForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const contactId = contactIdInput.value;
        const contactNumber = contactNumberInput.value;

        if (!contactId) {
            console.error('No contact ID found. Cannot update contact.');
            alert('Error: Contact ID is missing.');
            return;
        }

        console.log("Submitting update for Contact ID:", contactId); // Log to verify ID on submit

        // Use the correct route based on ID type
        const updateUrl = `/contacts/${contactId}/update-number?type=${contactIdType}`;

        fetch(updateUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ contact_number: contactNumber })
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                document.querySelector(`[data-id="${contactId}"]`).closest('tr').querySelector('td:nth-child(3)').textContent = contactNumber;

                window.dispatchEvent(new CustomEvent('close-modal', { detail: 'editContactModal' }));

                contactIdInput.value = '';
                contactNameInput.value = '';
                contactNumberInput.value = '';
            } else {
                alert('Failed to update contact number.');
            }
        })
        .catch(error => console.error('Error updating contact number:', error));
    });

    campusFilter.addEventListener('change', fetchContacts);
    searchInput.addEventListener('input', fetchContacts);
    typeFilter.addEventListener('change', fetchContacts);

    initializeEditButtons();
});
