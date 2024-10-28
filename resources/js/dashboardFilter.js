// dashboardFilter.js
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form');
    const messageTableBody = document.getElementById('messageTableBody');
    const paginationContainer = document.getElementById('paginationContainer');

    form.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent page refresh

        // Collect filter values
        const search = form.querySelector('input[name="search"]').value;
        const recipientType = form.querySelector('select[name="recipient_type"]').value;
        const status = form.querySelector('select[name="status"]').value;

        // Build the query string
        const queryString = new URLSearchParams({
            search,
            recipient_type: recipientType,
            status
        }).toString();

        // Send AJAX request
        fetch(`/dashboard?${queryString}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest' // Indicate AJAX request
            }
        })
            .then(response => response.json())
            .then(data => {
                updateTable(data.messageLogs);
                updatePagination(data.pagination);
            })
            .catch(error => console.error('Error fetching filtered data:', error));
    });

    function updateTable(logs) {
        messageTableBody.innerHTML = ''; // Clear existing table rows

        if (logs.length === 0) {
            messageTableBody.innerHTML = '<tr><td colspan="14" class="text-center py-4">No messages have been logged yet.</td></tr>';
            return;
        }

        logs.forEach(log => {
            const row = `
                <tr>
                    <td class="border px-4 py-2">${log.user ? log.user.name : 'Unknown'}</td>
                    <td class="border px-4 py-2">${log.campus ? log.campus.campus_name : 'All Campuses'}</td>
                    <td class="border px-4 py-2">${capitalize(log.recipient_type)}</td>
                    <td class="border px-4 py-2">${log.content || 'No Content'}</td>
                    <td class="border px-4 py-2">${capitalize(log.message_type)}</td>
                    <td class="border px-4 py-2">${log.total_recipients || 'N/A'}</td>
                    <td class="border px-4 py-2">${log.sent_count || 0}</td>
                    <td class="border px-4 py-2">${log.failed_count || 0}</td>
                    <td class="border px-4 py-2">${capitalize(log.status)}</td>
                    <td class="border px-4 py-2">${formatDate(log.created_at)}</td>
                    <td class="border px-4 py-2">${log.sent_at ? formatDate(log.sent_at) : 'N/A'}</td>
                    <td class="border px-4 py-2">${log.scheduled_at ? formatDate(log.scheduled_at) : 'N/A'}</td>
                    <td class="border px-4 py-2">${log.cancelled_at ? formatDate(log.cancelled_at) : 'N/A'}</td>
                    <td>${log.status === 'pending' && log.message_type === 'scheduled' ? createCancelButton(log.id) : ''}</td>
                </tr>
            `;
            messageTableBody.insertAdjacentHTML('beforeend', row);
        });
    }

    function updatePagination(pagination) {
        paginationContainer.innerHTML = pagination;
    }

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')} ${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
    }

    function createCancelButton(logId) {
        return `
            <form action="/messages/cancel/${logId}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this scheduled message?');">
                <input type="hidden" name="_method" value="PATCH">
                <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
            </form>
        `;
    }
});
