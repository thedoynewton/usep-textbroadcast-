document.addEventListener("DOMContentLoaded", function () {
    // Reference to the loading screen element
    const loadingScreen = document.getElementById("loadingScreen");

    // Handle Add Campus Button (only if on the DB Connection section)
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
            if (loadingScreen) loadingScreen.classList.remove("hidden");

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
                    addCampusCardToDOM(data.cardHtml);
                }

                if (data.campus) {
                    addCampusToTable(data.campus);
                }

                // Reset the form but do not close the modal
                addCampusForm.reset();
            } catch (error) {
                console.error("Error adding campus:", error);
                alert("There was an error adding the campus. Please try again.");
            } finally {
                // Hide loading screen
                if (loadingScreen) loadingScreen.classList.add("hidden");
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

        tableBody.appendChild(newRow);
    }

    // Handle Import Modal Functionality
    const importModal = document.getElementById("importModal");
    const importModalTitle = document.getElementById("importModalTitle");
    const closeImportModalButton = document.getElementById("closeImportModal");

    if (importModal && importModalTitle && closeImportModalButton) {
        const campusCardsContainer = document.getElementById("campusCardsContainer");

        if (campusCardsContainer) {
            campusCardsContainer.addEventListener("click", function (e) {
                const campusCard = e.target.closest("[data-campus-id]");
                if (campusCard) {
                    const campusId = campusCard.getAttribute("data-campus-id");
                    const campusName = campusCard.getAttribute("data-campus-name");

                    console.log("Selected Campus ID:", campusId);

                    importModalTitle.textContent = `${campusName} Import Options`;

                    document.querySelectorAll("#importModal .campus-id-input").forEach(input => {
                        input.value = campusId;
                    });

                    importModal.classList.remove("hidden");
                }
            });
        }

        document.querySelectorAll("#importModal form").forEach(form => {
            form.addEventListener("submit", async function (e) {
                e.preventDefault();

                const formData = new FormData(form);
                if (loadingScreen) loadingScreen.classList.remove("hidden");

                try {
                    const response = await fetch(form.action, {
                        method: "POST",
                        headers: {
                            "X-CSRF-Token": document.querySelector("meta[name='csrf-token']").content,
                        },
                        body: formData,
                    });

                    if (!response.ok) throw new Error("Failed to import data.");

                    alert("Data imported successfully!");
                } catch (error) {
                    console.error("Error importing data:", error);
                    alert("There was an error importing the data. Please try again.");
                } finally {
                    if (loadingScreen) loadingScreen.classList.add("hidden");
                }
            });
        });

        closeImportModalButton.addEventListener("click", () => {
            importModal.classList.add("hidden");
            document.querySelectorAll("#importModal .campus-id-input").forEach(input => {
                input.value = "";
            });
        });
    } else {
        console.log("Import modal elements not found. This is expected on non-campus pages.");
    }
});
