// generateReport.js
document.addEventListener("DOMContentLoaded", () => {
    const generateReportButton = document.getElementById("generateReportButton");
    const reportDropdown = document.getElementById("reportDropdown");
    const downloadCsv = document.getElementById("downloadCsv");
    const downloadPdf = document.getElementById("downloadPdf");

    // Toggle the dropdown visibility when the main button is clicked
    generateReportButton.addEventListener("click", (event) => {
        event.preventDefault();
        reportDropdown.classList.toggle("hidden");
    });

    // Close the dropdown if clicked outside
    document.addEventListener("click", (event) => {
        if (!generateReportButton.contains(event.target) && !reportDropdown.contains(event.target)) {
            reportDropdown.classList.add("hidden");
        }
    });

    // Function to handle report download
    function downloadReport(format) {
        fetch(`/generate-report?format=${format}`, {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.blob(); // Get the response as a blob for file download
            })
            .then((blob) => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement("a");
                a.style.display = "none";
                a.href = url;
                a.download = `message_logs_report.${format}`; // Set file name based on format
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url); // Clean up the URL
            })
            .catch((error) => console.error("Error generating report:", error));
    }

    // Event listeners for download options
    downloadCsv.addEventListener("click", (event) => {
        event.preventDefault();
        downloadReport("csv");
        reportDropdown.classList.add("hidden"); // Hide dropdown after selection
    });

    downloadPdf.addEventListener("click", (event) => {
        event.preventDefault();
        downloadReport("pdf");
        reportDropdown.classList.add("hidden"); // Hide dropdown after selection
    });
});
