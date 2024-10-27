document.addEventListener('DOMContentLoaded', function () {
    const totalMessagesCard = document.getElementById('totalMessagesCard');
    const scheduledMessagesCard = document.getElementById('scheduledMessagesCard');
    const immediateMessagesCard = document.getElementById('immediateMessagesCard');
    const failedMessagesCard = document.getElementById('failedMessagesCard');
    const recipientsModal = document.getElementById('recipientsModal');
    const recipientList = document.getElementById('recipientList');
    const closeModal = document.getElementById('closeModal');
    const modalOverlay = recipientsModal.querySelector('.bg-gray-500'); // Modal background overlay
    const paginationContainer = document.getElementById('paginationContainer'); // For pagination links

    // Function to fetch recipients with pagination
    function fetchRecipients(messageType, page = 1) {
        fetch(`/recipients?type=${messageType}&page=${page}`)
            .then(response => response.json())
            .then(data => {
                recipientList.innerHTML = ''; // Clear previous data

                data.recipients.data.forEach(recipient => {
                    const li = document.createElement('li');
                    li.classList.add('py-2');
                    li.innerHTML = `
                        <div><strong>Name:</strong> ${recipient.fname} ${recipient.lname}</div>
                        <div><strong>Email:</strong> ${recipient.email}</div>
                        <div><strong>Phone:</strong> ${recipient.c_num}</div>
                    `;
                    recipientList.appendChild(li);
                });

                // Update pagination links
                renderPagination(data.recipients, messageType);
                recipientsModal.classList.remove('hidden'); // Show the modal
            })
            .catch(error => console.error('Error fetching recipients:', error));
    }

    // Function to render pagination links
    function renderPagination(paginationData, messageType) {
        paginationContainer.innerHTML = ''; // Clear previous pagination

        // Add "Previous" button
        if (paginationData.prev_page_url) {
            const prevButton = document.createElement('button');
            prevButton.classList.add('px-4', 'py-2', 'rounded', 'border', 'text-gray-700', 'hover:bg-gray-200');
            prevButton.innerHTML = 'Previous';
            prevButton.addEventListener('click', function () {
                fetchRecipients(messageType, paginationData.current_page - 1);
            });
            paginationContainer.appendChild(prevButton);
        }

        // Generate page number buttons with ellipses
        const totalPages = paginationData.last_page;
        const currentPage = paginationData.current_page;
        const maxPages = 5; // Maximum number of page buttons to display

        for (let i = 1; i <= totalPages; i++) {
            if (
                i === 1 || i === totalPages || // Always show first and last page
                (i >= currentPage - 1 && i <= currentPage + 1) // Show pages around the current page
            ) {
                const pageButton = document.createElement('button');
                pageButton.classList.add('px-4', 'py-2', 'rounded', 'border', 'text-gray-700');

                // Disable current page button
                if (i === currentPage) {
                    pageButton.classList.add('bg-gray-300', 'cursor-not-allowed');
                } else {
                    pageButton.classList.add('hover:bg-gray-200');
                    pageButton.addEventListener('click', function () {
                        fetchRecipients(messageType, i);
                    });
                }
                pageButton.innerHTML = i;
                paginationContainer.appendChild(pageButton);
            } else if (
                (i === currentPage - 2 || i === currentPage + 2) && totalPages > maxPages // Show ellipses
            ) {
                const ellipsis = document.createElement('span');
                ellipsis.classList.add('px-3', 'py-2');
                ellipsis.innerHTML = '...';
                paginationContainer.appendChild(ellipsis);
            }
        }

        // Add "Next" button
        if (paginationData.next_page_url) {
            const nextButton = document.createElement('button');
            nextButton.classList.add('px-4', 'py-2', 'rounded', 'border', 'text-gray-700', 'hover:bg-gray-200');
            nextButton.innerHTML = 'Next';
            nextButton.addEventListener('click', function () {
                fetchRecipients(messageType, paginationData.current_page + 1);
            });
            paginationContainer.appendChild(nextButton);
        }
    }

    // Event listeners for the cards
    totalMessagesCard.addEventListener('click', () => fetchRecipients('total'));
    scheduledMessagesCard.addEventListener('click', () => fetchRecipients('scheduled'));
    immediateMessagesCard.addEventListener('click', () => fetchRecipients('instant'));
    failedMessagesCard.addEventListener('click', () => fetchRecipients('failed'));

    // Close the modal when the close button is clicked
    closeModal.addEventListener('click', function () {
        recipientsModal.classList.add('hidden');
    });

    // Close the modal when clicking outside the modal content
    modalOverlay.addEventListener('click', function () {
        recipientsModal.classList.add('hidden');
    });
});
