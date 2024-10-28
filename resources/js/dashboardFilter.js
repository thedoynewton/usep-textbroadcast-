// dashboardFilter.js
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("form");
    const messageTableBody = document.getElementById("messageTableBody");
    const paginationContainer = document.getElementById("paginationContainer");
    const searchInput = document.getElementById("searchInput");

    // Debounce function to limit the frequency of AJAX calls
    function debounce(func, delay) {
        let debounceTimer;
        return function (...args) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Trigger search on input change with debounce
    searchInput.addEventListener("input", debounce(handleSearch, 300));

    // Handles search and filter form submission
    form.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent page refresh
        handleSearch(); // Trigger search with current form data
    });

    // Main function to fetch and update table based on search and filters
    function handleSearch() {
        const searchTerm = searchInput.value;
        const recipientType = form.querySelector('select[name="recipient_type"]').value;
        const status = form.querySelector('select[name="status"]').value;

        const queryString = new URLSearchParams({
            search: searchTerm,
            recipient_type: recipientType,
            status,
        }).toString();

        // Send AJAX request
        fetch(`/dashboard?${queryString}`, {
            headers: {
                "X-Requested-With": "XMLHttpRequest", // Indicate AJAX request
            },
        })
            .then((response) => response.json())
            .then((data) => {
                updateTable(data.messageLogs, searchTerm); // Pass searchTerm to updateTable
                updatePagination(data.pagination);
            })
            .catch((error) => console.error("Error fetching filtered data:", error));
    }

    function updateTable(logs, searchTerm) {
        messageTableBody.innerHTML = ""; // Clear existing table rows

        if (logs.length === 0) {
            messageTableBody.innerHTML = '<tr><td colspan="14" class="text-center py-4">No messages have been logged yet.</td></tr>';
            return;
        }

        logs.forEach((log) => {
            const row = `
                <tr>
                    <td class="border px-4 py-2">${highlightText(log.user ? log.user.name : "Unknown", searchTerm)}</td>
                    <td class="border px-4 py-2">${highlightText(log.campus ? log.campus.campus_name : "All Campuses", searchTerm)}</td>
                    <td class="border px-4 py-2">${highlightText(capitalize(log.recipient_type), searchTerm)}</td>
                    <td class="border px-4 py-2">${highlightText(log.content || "No Content", searchTerm)}</td>
                    <td class="border px-4 py-2">${highlightText(capitalize(log.message_type), searchTerm)}</td>
                    <td class="border px-4 py-2">${log.total_recipients || "N/A"}</td>
                    <td class="border px-4 py-2">${log.sent_count || 0}</td>
                    <td class="border px-4 py-2">${log.failed_count || 0}</td>
                    <td class="border px-4 py-2">${highlightText(capitalize(log.status), searchTerm)}</td>
                    <td class="border px-4 py-2">${formatDate(log.created_at)}</td>
                    <td class="border px-4 py-2">${log.sent_at ? formatDate(log.sent_at) : "N/A"}</td>
                    <td class="border px-4 py-2">${log.scheduled_at ? formatDate(log.scheduled_at) : "N/A"}</td>
                    <td class="border px-4 py-2">${log.cancelled_at ? formatDate(log.cancelled_at) : "N/A"}</td>
                    <td>${log.status === "pending" && log.message_type === "scheduled" ? createCancelButton(log.id) : ""}</td>
                </tr>
            `;
            messageTableBody.insertAdjacentHTML("beforeend", row);
        });
    }

    // Function to wrap the search term with a highlighted span using inline styles
    function highlightText(text, searchTerm) {
        if (!searchTerm) return text; // Return the original text if no search term

        const regex = new RegExp(`(${searchTerm})`, "gi"); // Case-insensitive match
        return text.replace(
            regex,
            `<span style="background-color: yellow; font-weight: bold;">$1</span>`
        ); // Inline styles for highlight
    }

    function updatePagination(pagination) {
        paginationContainer.innerHTML = pagination;
    }

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return `${date.getFullYear()}-${(date.getMonth() + 1)
            .toString()
            .padStart(2, "0")}-${date
            .getDate()
            .toString()
            .padStart(2, "0")} ${date
            .getHours()
            .toString()
            .padStart(2, "0")}:${date
            .getMinutes()
            .toString()
            .padStart(2, "0")}`;
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
