document.addEventListener("DOMContentLoaded", function () {
    // Pie chart for messages by category
    const ctxCategory = document
        .getElementById("messagesByCategoryChart")
        ?.getContext("2d");
    if (ctxCategory) {
        const categoryLabels = JSON.parse(
            ctxCategory.canvas.dataset.categoryLabels || "[]"
        );
        const categoryCounts = JSON.parse(
            ctxCategory.canvas.dataset.categoryCounts || "[]"
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
    }

    // Bar chart for messages by recipient type
    const ctxRecipientType = document
        .getElementById("messagesByRecipientTypeChart")
        ?.getContext("2d");
    if (ctxRecipientType) {
        const recipientTypes = JSON.parse(
            ctxRecipientType.canvas.dataset.recipientTypes || "[]"
        );
        const recipientCounts = JSON.parse(
            ctxRecipientType.canvas.dataset.recipientCounts || "[]"
        );

        new Chart(ctxRecipientType, {
            type: "bar",
            data: {
                labels: recipientTypes,
                datasets: [
                    {
                        label: "Messages Sent",
                        data: recipientCounts,
                        backgroundColor: "#4CAF50",
                        borderColor: "#4CAF50",
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
                        ticks: {
                            stepSize: 1, // Ensure step increments by 1
                            callback: function (value) {
                                return Math.round(value); // Ensure only integers are displayed
                            },
                        },
                    },
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                },
            },
        });
    }

    // Grouped bar chart for messages by status
    const ctxStatus = document.getElementById("messagesByStatusChart");
    if (ctxStatus) {
        let dates = JSON.parse(ctxStatus.dataset.statusDates || "[]"); // Use let here
        let statusData = JSON.parse(ctxStatus.dataset.statusData || "[]");

        // Ensure 'dates' is an array and convert if it's an object
        if (typeof dates === "object" && !Array.isArray(dates)) {
            dates = Object.values(dates); // Convert object values to an array
        }

        // Ensure 'dates' is an array
        if (!Array.isArray(dates)) {
            console.error("Invalid dates format:", dates);
            return;
        }

        // Assign colors for statuses
        const statusColors = {
            Sent: "#4CAF50",
            Failed: "#F44336",
            Cancelled: "#FF9800",
            Pending: "#2196F3",
        };

        // Map over statusData and add colors dynamically based on labels
        const updatedStatusData = statusData.map((dataset) => ({
            ...dataset,
            backgroundColor: statusColors[dataset.label] || "#999999",
            borderColor: statusColors[dataset.label] || "#999999",
        }));

        // Create the chart
        new Chart(ctxStatus.getContext("2d"), {
            type: "bar",
            data: {
                labels: dates,
                datasets: updatedStatusData,
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: false,
                        barPercentage: 0.5,
                        categoryPercentage: 0.7,
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1, // Fixed step size of 1
                            callback: function (value) {
                                return Math.round(value); // Ensure values are integers
                            },
                        },
                    },
                },
                plugins: {
                    legend: {
                        position: "top",
                    },
                },
            },
        });
    }

    // Function to export data to CSV
    function exportToCSV(filename, rows) {
        const csvContent = rows.map((row) => row.join(",")).join("\n");
        const blob = new Blob([csvContent], {
            type: "text/csv;charset=utf-8;",
        });
        const link = document.createElement("a");
        const url = URL.createObjectURL(blob);
        link.setAttribute("href", url);
        link.setAttribute("download", filename);
        link.style.visibility = "hidden";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Handle Export for Status Data
    document
        .getElementById("exportStatusData")
        ?.addEventListener("click", () => {
            let dates = JSON.parse(
                document
                    .getElementById("messagesByStatusChart")
                    ?.getAttribute("data-status-dates") || "[]"
            );

            let statusData = JSON.parse(
                document
                    .getElementById("messagesByStatusChart")
                    ?.getAttribute("data-status-data") || "[]"
            );

            // Ensure 'dates' is an array. If it's an object, convert to array using Object.values().
            if (typeof dates === "object" && !Array.isArray(dates)) {
                dates = Object.values(dates); // Convert object values to an array
            }

            // If 'dates' is not an array or it's an empty array, log an error and stop the execution
            if (!Array.isArray(dates) || dates.length === 0) {
                console.error("Invalid or empty dates format:", dates);
                return;
            }

            // Prepare header row with all statuses dynamically from the datasets
            const statusKeys = statusData.map((dataset) => dataset.label); // Extract all status labels
            const rows = [["Date", ...statusKeys]];

            // Map each date to the respective status counts
            dates.forEach((date, dateIndex) => {
                const row = [date];
                statusKeys.forEach((status) => {
                    // Find the dataset for the current status
                    const dataset = statusData.find(
                        (dataset) => dataset.label === status
                    );
                    // Add the corresponding value or 0 if no data exists
                    row.push(dataset ? dataset.data[dateIndex] || 0 : 0);
                });
                rows.push(row);
            });

            // Call function to export data to CSV
            exportToCSV("messages_by_status.csv", rows);
        });

    // Handle Export for Category Data
    document
        .getElementById("exportCategoryData")
        ?.addEventListener("click", () => {
            const labels = JSON.parse(
                document
                    .getElementById("messagesByCategoryChart")
                    .getAttribute("data-category-labels") || "[]"
            );
            const counts = JSON.parse(
                document
                    .getElementById("messagesByCategoryChart")
                    .getAttribute("data-category-counts") || "[]"
            );

            const rows = [["Category", "Count"]];
            labels.forEach((label, index) => {
                rows.push([label, counts[index]]);
            });

            exportToCSV("messages_by_category.csv", rows);
        });

    // Handle Export for Recipient Type Data
    document
        .getElementById("exportRecipientTypeData")
        ?.addEventListener("click", () => {
            const types = JSON.parse(
                document
                    .getElementById("messagesByRecipientTypeChart")
                    .getAttribute("data-recipient-types") || "[]"
            );
            const counts = JSON.parse(
                document
                    .getElementById("messagesByRecipientTypeChart")
                    .getAttribute("data-recipient-counts") || "[]"
            );

            const rows = [["Recipient Type", "Count"]];
            types.forEach((type, index) => {
                rows.push([type, counts[index]]);
            });

            exportToCSV("messages_by_recipient_type.csv", rows);
        });

    // Date filter handling
    document
        .getElementById("applyFilter")
        ?.addEventListener("click", function () {
            const startDate = document.getElementById("startDate")?.value || "";
            const endDate = document.getElementById("endDate")?.value || "";

            if (startDate && endDate) {
                fetch(
                    `/analytics/data?startDate=${startDate}&endDate=${endDate}`
                )
                    .then((response) => response.json())
                    .then((data) => {
                        updateCharts(data);
                    })
                    .catch((error) => {
                        console.error("Error fetching filtered data:", error);
                    });
            } else {
                console.error("Start Date or End Date is missing.");
            }
        });

    // Update charts with new data
    function updateCharts(data) {
        console.log(data); // Debug and refresh charts here
        // Here, you'd typically update the charts with the new data
    }
});
