// Tab switch function
window.showTab = function(contentId, tabId) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));

    // Remove active state from all tabs
    document.getElementById('messageOverviewTab').classList.remove('border-indigo-500', 'text-indigo-500');
    document.getElementById('messageOverviewTab').classList.add('text-gray-600', 'border-transparent');
    document.getElementById('costOverviewTab').classList.remove('border-indigo-500', 'text-indigo-500');
    document.getElementById('costOverviewTab').classList.add('text-gray-600', 'border-transparent');

    // Show the selected tab content and set the selected tab to active
    document.getElementById(contentId).classList.remove('hidden');
    document.getElementById(tabId).classList.add('border-indigo-500', 'text-indigo-500');
    document.getElementById(tabId).classList.remove('text-gray-600', 'border-transparent');
};

// Initialize Message Overview Chart (Bar)
const messageDates = JSON.parse(document.getElementById('messagesOverviewChart').dataset.messageDates);
const successCounts = JSON.parse(document.getElementById('messagesOverviewChart').dataset.successCounts);
const failedCounts = JSON.parse(document.getElementById('messagesOverviewChart').dataset.failedCounts);

const ctxMessages = document.getElementById('messagesOverviewChart').getContext('2d');
const messageChart = new Chart(ctxMessages, {
    type: 'bar',
    data: {
        labels: messageDates,
        datasets: [
            { label: 'Success', data: successCounts, backgroundColor: 'rgba(75, 192, 192, 0.6)', borderColor: 'rgba(75, 192, 192, 1)', borderWidth: 1 },
            { label: 'Failed', data: failedCounts, backgroundColor: 'rgba(255, 99, 132, 0.6)', borderColor: 'rgba(255, 99, 132, 1)', borderWidth: 1 }
        ]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true, title: { display: true, text: 'Message Count' } } } }
});

// Initialize Costs Overview Chart (Line)
const costDates = JSON.parse(document.getElementById('costsChart').dataset.costDates);
const costs = JSON.parse(document.getElementById('costsChart').dataset.costs);

const ctxCosts = document.getElementById('costsChart').getContext('2d');
const costChart = new Chart(ctxCosts, {
    type: 'line',
    data: {
        labels: costDates,
        datasets: [{
            label: 'Cost ($)',
            data: costs,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Cost in USD ($)' },
                ticks: { callback: function(value) { return value.toFixed(4); } }
            }
        },
        plugins: {
            tooltip: {
                callbacks: { label: function(context) { return 'Cost ($): ' + context.raw.toFixed(4); } }
            }
        }
    }
});

// Event listeners for filter forms
document.addEventListener("DOMContentLoaded", function () {
    // Date Range Filter - Applies to both charts
    document.getElementById("dateRangeFilterForm").addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const queryString = new URLSearchParams(formData).toString();

        // Update both Message and Costs Overview charts based on date range
        updateMessageOverviewChart(queryString);
        updateCostsOverviewChart(queryString);
    });

    // Message-Specific Filter - Applies only to Message Overview
    document.getElementById("messageFilterForm").addEventListener("submit", function (e) {
        e.preventDefault();

        // Combine all filters (date range + specific filters)
        const dateRangeForm = document.getElementById("dateRangeFilterForm");
        const messageForm = document.getElementById("messageFilterForm");
        const combinedFormData = new FormData();

        // Append date range filters
        Array.from(dateRangeForm.elements).forEach(element => {
            if (element.name && element.value) combinedFormData.append(element.name, element.value);
        });

        // Append message-specific filters
        Array.from(messageForm.elements).forEach(element => {
            if (element.name && element.value) combinedFormData.append(element.name, element.value);
        });

        const queryString = new URLSearchParams(combinedFormData).toString();

        // Update only the Message Overview chart
        updateMessageOverviewChart(queryString);
    });
});

// Function to update Message Overview chart
function updateMessageOverviewChart(queryString) {
    fetch(`/api/analytics/message-overview?${queryString}`)
        .then(response => response.json())
        .then(data => {
            updateChart(messageChart, data.messageDates, [
                { label: 'Success', data: data.successCounts, backgroundColor: 'rgba(75, 192, 192, 0.6)', borderColor: 'rgba(75, 192, 192, 1)', borderWidth: 1 },
                { label: 'Failed', data: data.failedCounts, backgroundColor: 'rgba(255, 99, 132, 0.6)', borderColor: 'rgba(255, 99, 132, 1)', borderWidth: 1 }
            ]);
        })
        .catch(error => console.error("Error fetching message overview data:", error));
}

// Function to update Costs Overview chart
function updateCostsOverviewChart(queryString) {
    fetch(`/api/analytics/costs-overview?${queryString}`)
        .then(response => response.json())
        .then(data => {
            updateChart(costChart, data.costDates, [
                { label: 'Cost ($)', data: data.costs, backgroundColor: 'rgba(54, 162, 235, 0.2)', borderColor: 'rgba(54, 162, 235, 1)', borderWidth: 2, fill: true, tension: 0.1 }
            ]);
        })
        .catch(error => console.error("Error fetching costs overview data:", error));
}

// Helper function to update a chart
function updateChart(chart, newLabels, newDataSets) {
    chart.data.labels = newLabels;
    chart.data.datasets = newDataSets;
    chart.update();
}
