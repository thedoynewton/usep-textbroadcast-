document.addEventListener('DOMContentLoaded', function () {
    // Pie chart for messages by category
    const ctxCategory = document.getElementById('messagesByCategoryChart').getContext('2d');
    const categoryLabels = JSON.parse(ctxCategory.canvas.dataset.categoryLabels);
    const categoryCounts = JSON.parse(ctxCategory.canvas.dataset.categoryCounts);

    new Chart(ctxCategory, {
        type: 'pie',
        data: {
            labels: categoryLabels,
            datasets: [{
                label: 'Messages Category',
                data: categoryCounts,
                backgroundColor: [
                    '#4dc9f6', '#f67019', '#f53794', '#537bc4', '#acc236', '#166a8f', '#00a950', '#58595b', '#8549ba'
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // Bar chart for messages by recipient type
    const ctxRecipientType = document.getElementById('messagesByRecipientTypeChart').getContext('2d');
    const recipientTypes = JSON.parse(ctxRecipientType.canvas.dataset.recipientTypes);
    const recipientCounts = JSON.parse(ctxRecipientType.canvas.dataset.recipientCounts);

    new Chart(ctxRecipientType, {
        type: 'bar',
        data: {
            labels: recipientTypes,
            datasets: [{
                label: 'Messages Sent',
                data: recipientCounts,
                backgroundColor: '#4dc9f6',
                borderColor: '#4dc9f6',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Bar chart for messages by status
    const ctxStatus = document.getElementById('messagesByStatusChart').getContext('2d');
    const statusLabels = JSON.parse(ctxStatus.canvas.dataset.statusLabels);
    const statusCounts = JSON.parse(ctxStatus.canvas.dataset.statusCounts);

    new Chart(ctxStatus, {
        type: 'bar',
        data: {
            labels: statusLabels,
            datasets: [{
                label: 'Message Status',
                data: statusCounts,
                backgroundColor: '#34d399',
                borderColor: '#059669',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
