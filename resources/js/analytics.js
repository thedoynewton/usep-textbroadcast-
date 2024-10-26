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

// Data for Messages Overview Chart (Bar)
const messageDates = JSON.parse(document.getElementById('messagesOverviewChart').dataset.messageDates);
const successCounts = JSON.parse(document.getElementById('messagesOverviewChart').dataset.successCounts);
const failedCounts = JSON.parse(document.getElementById('messagesOverviewChart').dataset.failedCounts);

const ctxMessages = document.getElementById('messagesOverviewChart').getContext('2d');
new Chart(ctxMessages, {
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

// Data for Costs Overview Chart (Line)
const costDates = JSON.parse(document.getElementById('costsChart').dataset.costDates);
const costs = JSON.parse(document.getElementById('costsChart').dataset.costs);

const ctxCosts = document.getElementById('costsChart').getContext('2d');
new Chart(ctxCosts, {
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
