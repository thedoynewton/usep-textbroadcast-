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

                if (data.campus) {
                    // Add the new campus to the table
                    addCampusToTable(data.campus);
                }

                // Reset the form but do not close the modal
                addCampusForm.reset();
            } catch (error) {
                console.error("Error adding campus:", error);
                alert(
                    "There was an error adding the campus. Please try again."
                );
            } finally {
                // Hide loading screen
                if (loadingScreen) loadingScreen.classList.add("hidden");
            }
        });
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

        newRow.setAttribute("data-campus-id", campus.campus_id); // Set campus_id as a data attribute

        newRow.innerHTML = `
            <td class="py-2 px-4 border-b">${campus.campus_id}</td>
            <td class="py-2 px-4 border-b">${campus.campus_name}</td>
        `;

        tableBody.appendChild(newRow);
    }

    // Add event listener to each row to open the modal
    const tableBody = document.querySelector("table tbody");
    if (tableBody) {
        tableBody.addEventListener("click", function (event) {
            const clickedRow = event.target.closest("tr"); // Get the clicked row

            if (clickedRow && clickedRow.hasAttribute("data-campus-id")) {
                const campusId = clickedRow.getAttribute("data-campus-id"); // Get the campus_id from the clicked row
                const campusName = clickedRow.cells[1].innerText; // Get the campus_name from the clicked row

                console.log("Campus ID:", campusId);
                
                // Set the campus_id in the hidden input fields inside the modal
                const campusIdInputs =
                    document.querySelectorAll(".campus-id-input");
                campusIdInputs.forEach((input) => {
                    input.value = campusId;
                });

                // Set the campus_name in the modal
                const campusNameDisplay =
                    document.getElementById("importModalTitle");
                if (campusNameDisplay) {
                    campusNameDisplay.innerText = `Import Data for ${campusName}`;
                }

                // Open the modal
                document
                    .getElementById("importModal")
                    .classList.remove("hidden");
            }
        });
    }

    // Close the modal when clicking on the Close button
    const closeModalButton = document.getElementById("closeImportModal");
    if (closeModalButton) {
        closeModalButton.addEventListener("click", function () {
            document.getElementById("importModal").classList.add("hidden");
        });
    }
});
