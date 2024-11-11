document.addEventListener("DOMContentLoaded", function () {
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

                // Reset the form and close the modal
                addCampusForm.reset();
                window.dispatchEvent(
                    new CustomEvent("close-modal", { detail: "addCampusModal" })
                );
            } catch (error) {
                console.error("Error adding campus:", error);
                alert(
                    "There was an error adding the campus. Please try again."
                );
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
    const modalCampusId = document.getElementById("modalCampusId"); // Hidden input for campus_id
    const closeImportModalButton = document.getElementById("closeImportModal");

    if (
        importModal &&
        importModalTitle &&
        closeImportModalButton &&
        modalCampusId
    ) {
        document
            .getElementById("campusCardsContainer")
            .addEventListener("click", function (e) {
                const campusCard = e.target.closest("[data-campus-id]");
                if (campusCard) {
                    const campusId = campusCard.getAttribute("data-campus-id"); // Get campus_id from card
                    const campusName =
                        campusCard.getAttribute("data-campus-name"); // Get campus_name from card
                    // Log the campus_id to the console
                    console.log("Campus ID:", campusId);
                    // Set the modal title and campus_id for the forms in the modal
                    importModalTitle.textContent = `${campusName} Import Options`;
                    modalCampusId.value = campusId; // Set campus_id in the hidden input
                    importModal.classList.remove("hidden");
                }
            });

        closeImportModalButton.addEventListener("click", () => {
            importModal.classList.add("hidden");
        });

        window.addEventListener("click", function (e) {
            if (e.target === importModal) {
                importModal.classList.add("hidden");
            }
        });
    } else {
        console.error("Import modal elements not found.");
    }
});
