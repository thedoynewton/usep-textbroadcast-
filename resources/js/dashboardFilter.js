document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const recipientTypeFilter = document.querySelector('[name="recipient_type"]');
    const statusFilter = document.querySelector('[name="status"]');
    const messageLogsContainer = document.getElementById('messageLogsContainer');

    const recipientsModal = document.getElementById('recipientsModal');
    const recipientList = document.getElementById('recipientList');
    const closeRecipientsModal = document.getElementById('closeRecipientsModal');
    const modalOverlay = recipientsModal.querySelector('.bg-gray-500');

    function fetchMessageLogs(url = '/dashboard') {
        const search = searchInput.value;
        const recipientType = recipientTypeFilter.value;
        const status = statusFilter.value;

        url += `?search=${encodeURIComponent(search)}&recipient_type=${encodeURIComponent(recipientType)}&status=${encodeURIComponent(status)}`;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            messageLogsContainer.innerHTML = data.html;
            initializePaginationLinks();
            initializeRowToggles();
            initializeRecipientLinks(); // Initialize recipient links for new data
            if (search) {
                highlightSearchTerm(search);
            }
        })
        .catch(error => console.error('Error fetching message logs:', error));
    }

    function highlightSearchTerm(term) {
        const regex = new RegExp(`(${term})`, 'gi');
        function highlightTextNodes(node) {
            if (node.nodeType === Node.TEXT_NODE) {
                const matches = node.textContent.match(regex);
                if (matches) {
                    const fragment = document.createDocumentFragment();
                    node.textContent.split(regex).forEach(part => {
                        const span = document.createElement('span');
                        if (part.toLowerCase() === term.toLowerCase()) {
                            span.style.backgroundColor = 'yellow';
                            span.textContent = part;
                        } else {
                            span.textContent = part;
                        }
                        fragment.appendChild(span);
                    });
                    node.replaceWith(fragment);
                }
            } else {
                node.childNodes.forEach(highlightTextNodes);
            }
        }
        highlightTextNodes(messageLogsContainer);
    }

    function initializePaginationLinks() {
        document.querySelectorAll('#paginationContainer .pagination a').forEach(link => {
            link.addEventListener('click', function (event) {
                event.preventDefault();
                fetchMessageLogs(this.getAttribute('href'));
            });
        });
    }

    function initializeRowToggles() {
        document.querySelectorAll('.toggle-row').forEach(row => {
            row.addEventListener('click', function () {
                const logId = this.getAttribute('data-log-id');
                const toggleRow = document.getElementById(`toggle-row-${logId}`);
                if (toggleRow.classList.contains('hidden')) {
                    toggleRow.classList.remove('hidden');
                } else {
                    toggleRow.classList.add('hidden');
                }
            });
        });
    }

    function initializeRecipientLinks() {
        document.querySelectorAll('.show-recipients').forEach(link => {
            link.addEventListener('click', function (event) {
                event.preventDefault();

                const logId = this.getAttribute('data-log-id');
                showRecipientsModal(logId); // Call function to open modal with recipients
            });
        });
    }

    // Function to show recipients modal and fetch data with pagination
    function showRecipientsModal(logId, pageUrl = `/recipients/${logId}`) {
        // Clear previous content
        recipientList.innerHTML = '<p class="text-center py-4 text-gray-500">Loading...</p>';
        recipientsModal.classList.remove('hidden'); // Show the modal

        fetch(pageUrl)
            .then(response => response.json())
            .then(data => {
                recipientList.innerHTML = ''; // Clear loading message
                if (data.recipients && data.recipients.data.length > 0) {
                    // Loop through recipients and display them
                    data.recipients.data.forEach(recipient => {
                        const recipientItem = document.createElement('li');
                        recipientItem.classList.add('py-2');
                        recipientItem.innerHTML = `
                            <div><strong>Name:</strong> ${recipient.fname} ${recipient.lname}</div>
                            <div><strong>Email:</strong> ${recipient.email}</div>
                            <div><strong>Phone:</strong> ${recipient.c_num}</div>
                        `;
                        recipientList.appendChild(recipientItem);
                    });

                    // Add pagination controls
                    renderPaginationControls(data.recipients, logId);
                } else {
                    recipientList.innerHTML = '<p class="text-center text-gray-500">No recipients found.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching recipients:', error);
                recipientList.innerHTML = '<p class="text-center text-red-500">Failed to load recipients.</p>';
            });
    }

    // Function to render pagination controls in the modal
    function renderPaginationControls(paginationData, logId) {
        const paginationContainer = document.createElement('div');
        paginationContainer.classList.add('mt-4', 'flex', 'justify-center', 'space-x-2');

        // Previous Page Button
        if (paginationData.prev_page_url) {
            const prevButton = document.createElement('button');
            prevButton.classList.add('px-4', 'py-2', 'rounded', 'border', 'text-gray-700', 'hover:bg-gray-200');
            prevButton.innerText = 'Previous';
            prevButton.addEventListener('click', () => {
                showRecipientsModal(logId, paginationData.prev_page_url);
            });
            paginationContainer.appendChild(prevButton);
        }

        // Next Page Button
        if (paginationData.next_page_url) {
            const nextButton = document.createElement('button');
            nextButton.classList.add('px-4', 'py-2', 'rounded', 'border', 'text-gray-700', 'hover:bg-gray-200');
            nextButton.innerText = 'Next';
            nextButton.addEventListener('click', () => {
                showRecipientsModal(logId, paginationData.next_page_url);
            });
            paginationContainer.appendChild(nextButton);
        }

        // Append pagination controls to the modal
        recipientList.appendChild(paginationContainer);
    }

    closeRecipientsModal.addEventListener('click', function () {
        recipientsModal.classList.add('hidden');
    });

    modalOverlay.addEventListener('click', function () {
        recipientsModal.classList.add('hidden');
    });

    searchInput.addEventListener('input', () => fetchMessageLogs());
    recipientTypeFilter.addEventListener('change', () => fetchMessageLogs());
    statusFilter.addEventListener('change', () => fetchMessageLogs());

    initializePaginationLinks();
    initializeRowToggles();
    initializeRecipientLinks();
});
