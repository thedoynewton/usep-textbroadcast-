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
                paginationContainer.innerHTML = ''; // Clear previous pagination

                if (data.recipients.links) {
                    data.recipients.links.forEach(link => {
                        const pageLink = document.createElement('button');
                        pageLink.classList.add('px-4', 'py-2', 'mx-1', 'rounded', 'border', 'text-gray-700');
                        pageLink.innerHTML = link.label;

                        // Disable the current page button
                        if (link.active) {
                            pageLink.classList.add('bg-gray-300', 'cursor-not-allowed');
                        } else {
                            pageLink.classList.add('hover:bg-gray-200');
                            pageLink.addEventListener('click', function () {
                                fetchRecipients(messageType, new URL(link.url).searchParams.get('page'));
                            });
                        }

                        paginationContainer.appendChild(pageLink);
                    });
                }

                recipientsModal.classList.remove('hidden');  // Show the modal
            })
            .catch(error => console.error('Error fetching recipients:', error));
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
