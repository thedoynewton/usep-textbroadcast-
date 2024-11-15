document.addEventListener("DOMContentLoaded", function () {
    // Pie chart for messages by category
    const ctxCategory = document
        .getElementById("messagesByCategoryChart")
        .getContext("2d");
    const categoryLabels = JSON.parse(
        ctxCategory.canvas.dataset.categoryLabels
    );
    const categoryCounts = JSON.parse(
        ctxCategory.canvas.dataset.categoryCounts
    );

    new Chart(ctxCategory, {
        type: "pie",
        data: {
            labels: categoryLabels,
            datasets: [
                {
                    label: "Messages Category",
                    data: categoryCounts,
                    backgroundColor: [
                        "#4dc9f6",
                        "#f67019",
                        "#f53794",
                        "#537bc4",
                        "#acc236",
                        "#166a8f",
                        "#00a950",
                        "#58595b",
                        "#8549ba",
                    ],
                    hoverOffset: 4,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "bottom",
                },
            },
        },
    });

    // Bar chart for messages by recipient type
    const ctxRecipientType = document
        .getElementById("messagesByRecipientTypeChart")
        .getContext("2d");
    const recipientTypes = JSON.parse(
        ctxRecipientType.canvas.dataset.recipientTypes
    );
    const recipientCounts = JSON.parse(
        ctxRecipientType.canvas.dataset.recipientCounts
    );

    new Chart(ctxRecipientType, {
        type: "bar",
        data: {
            labels: recipientTypes,
            datasets: [
                {
                    label: "Messages Sent",
                    data: recipientCounts,
                    backgroundColor: "#4dc9f6",
                    borderColor: "#4dc9f6",
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
            plugins: {
                legend: {
                    display: false,
                },
            },
        },
    });

 // Grouped bar chart for messages by status
 const ctxStatus = document.getElementById('messagesByStatusChart');
 if (ctxStatus) {
     const dates = JSON.parse(ctxStatus.dataset.statusDates || '[]'); // Dates for X-axis
     const statusData = JSON.parse(ctxStatus.dataset.statusData || '[]'); // Datasets for each status

     new Chart(ctxStatus.getContext('2d'), {
         type: 'bar',
         data: {
             labels: dates, // X-axis labels (unique dates)
             datasets: statusData // Status counts per date, each status is a separate dataset
         },
         options: {
             responsive: true,
             maintainAspectRatio: false,
             scales: {
                 x: {
                     stacked: false, // Group bars by status for each date
                     barPercentage: 0.5,
                     categoryPercentage: 0.7
                 },
                 y: {
                     beginAtZero: true,
                     ticks: {
                        stepSize: 1, // Ensure only whole numbers are displayed on y-axis
                        callback: function(value) {
                            return Number.isInteger(value) ? value : null; // Show only integers
                        }
                    }
                 }
             },
             plugins: {
                 legend: {
                     position: 'top' // Place legend at the top
                 }
             }
         }
     });
 }
});
