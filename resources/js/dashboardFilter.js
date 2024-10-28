document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const recipientTypeFilter = document.querySelector('[name="recipient_type"]');
    const statusFilter = document.querySelector('[name="status"]');
    const messageLogsContainer = document.getElementById('messageLogsContainer');

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
            if (search) {
                highlightSearchTerm(search);
            }
        })
        .catch(error => console.error('Error fetching message logs:', error));
    }

    // Highlight the search term within the message logs table
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

    searchInput.addEventListener('input', () => fetchMessageLogs());
    recipientTypeFilter.addEventListener('change', () => fetchMessageLogs());
    statusFilter.addEventListener('change', () => fetchMessageLogs());

    initializePaginationLinks();
});
