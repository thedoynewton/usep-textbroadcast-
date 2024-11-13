document.addEventListener("DOMContentLoaded", function () {
    // Reference to the loading screen element
    const loadingScreen = document.getElementById("loadingScreen");

    // Open Add Campus Modal
    const addCampusButton = document.getElementById("addCampusButton");
    if (addCampusButton) {
        addCampusButton.addEventListener("click", () => {
            window.dispatchEvent(
                new CustomEvent("open-modal", { detail: "addCampusModal" })
            );
        });
    }

    // Handle Add Campus Form Submission
    const addCampusForm = document.getElementById("addCampusForm");
    if (addCampusForm) {
        addCampusForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const formData = new FormData(addCampusForm);

            // Show loading screen
            loadingScreen.classList.remove("hidden");

            try {
                const response = await fetch("/campuses/add", {
                    method: "POST",
                    headers: {
                        "X-CSRF-Token": document.querySelector(
                            "meta[name='csrf-token']"
                        ).content,
                    },
                    body: formData,
                });

                if (!response.ok) throw new Error("Failed to add campus.");

                const data = await response.json();

                if (data.cardHtml) {
                    addCampusCardToDOM(data.cardHtml); // Add new campus card to DOM
                }

                if (data.campus) {
                    addCampusToTable(data.campus); // Add new campus row to table
                }

                // Reset the form but do not close the modal
                addCampusForm.reset();
            } catch (error) {
                console.error("Error adding campus:", error);
                alert("There was an error adding the campus. Please try again.");
            } finally {
                // Hide loading screen
                loadingScreen.classList.add("hidden");
            }
        });
    }

    // Function to add a campus card to the DOM
    function addCampusCardToDOM(cardHtml) {
        const container = document.getElementById("campusCardsContainer");
        if (container) {
            container.insertAdjacentHTML("beforeend", cardHtml);
        } else {
            console.error("Campus cards container not found.");
        }
    }

    // Function to add a campus row to the table
    function addCampusToTable(campus) {
        const tableBody = document.querySelector("table tbody");
        if (!tableBody) {
            console.error("Table body not found.");
            return;
        }

        // Create a new row for the added campus
        const newRow = document.createElement("tr");
        newRow.classList.add(
            "hover:bg-gray-100",
            "text-center",
            "border-b",
            "border-gray-300"
        );

        newRow.innerHTML = `
            <td class="py-2 px-4 border-b">${campus.campus_id}</td>
            <td class="py-2 px-4 border-b">${campus.campus_name}</td>
        `;

        // Append the new row to the table body
        tableBody.appendChild(newRow);
    }

    // Handle displaying the Import Modal with dynamic content
    const importModal = document.getElementById("importModal");
    const importModalTitle = document.getElementById("importModalTitle");
    const closeImportModalButton = document.getElementById("closeImportModal");

    if (importModal && importModalTitle && closeImportModalButton) {
        // Open import modal and set campus ID when a campus card is clicked
        document.getElementById("campusCardsContainer").addEventListener("click", function (e) {
            const campusCard = e.target.closest("[data-campus-id]");
            if (campusCard) {
                const campusId = campusCard.getAttribute("data-campus-id");
                const campusName = campusCard.getAttribute("data-campus-name");

                // Log campus_id for debugging purposes
                console.log("Selected Campus ID:", campusId);

                // Set the title and open modal
                importModalTitle.textContent = `${campusName} Import Options`;

                // Set campus_id in each form's hidden input field within the modal
                document.querySelectorAll("#importModal .campus-id-input").forEach(input => {
                    input.value = campusId;
                });

                importModal.classList.remove("hidden"); // Show modal
            }
        });

        // Prevent form submission from closing the modal by using AJAX
        document.querySelectorAll("#importModal form").forEach(form => {
            form.addEventListener("submit", async function (e) {
                e.preventDefault(); // Prevent default form submission

                const formData = new FormData(form);

                // Show loading screen
                loadingScreen.classList.remove("hidden");

                try {
                    const response = await fetch(form.action, {
                        method: "POST",
                        headers: {
                            "X-CSRF-Token": document.querySelector("meta[name='csrf-token']").content,
                        },
                        body: formData,
                    });

                    if (!response.ok) throw new Error("Failed to import data.");

                    // Show a success message without closing the modal
                    alert("Data imported successfully!");
                } catch (error) {
                    console.error("Error importing data:", error);
                    alert("There was an error importing the data. Please try again.");
                } finally {
                    // Hide loading screen
                    loadingScreen.classList.add("hidden");
                }
            });
        });

        // Close modal only when user clicks the close button
        closeImportModalButton.addEventListener("click", () => {
            importModal.classList.add("hidden"); // Close the modal
            document.querySelectorAll("#importModal .campus-id-input").forEach(input => {
                input.value = ""; // Clear campus_id for all forms on modal close
            });
        });
    } else {
        console.error("Import modal elements not found.");
    }
});
