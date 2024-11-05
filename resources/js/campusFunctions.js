document.addEventListener("DOMContentLoaded", function () {
    // Open Add Campus Modal
    const addCampusButton = document.getElementById("addCampusButton");
    addCampusButton.addEventListener("click", () => {
        window.dispatchEvent(new CustomEvent("open-modal", { detail: "addCampusModal" }));
    });

    // Handle Add Campus Form Submission
    const addCampusForm = document.getElementById("addCampusForm");
    addCampusForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const formData = new FormData(addCampusForm);

        try {
            const response = await fetch("/campuses/add", {
                method: "POST",
                headers: { "X-CSRF-Token": document.querySelector("meta[name='csrf-token']").content },
                body: formData,
            });
            const data = await response.json();
            addCampusToTable(data.campus);
            addCampusForm.reset();
            window.dispatchEvent(new CustomEvent("close-modal", { detail: "addCampusModal" }));
        } catch (error) {
            console.error("Error adding campus:", error);
        }
    });

    // Handle Edit Campus
    document.querySelectorAll(".edit-campus-btn").forEach((button) => {
        button.addEventListener("click", (e) => {
            const campusId = e.target.dataset.campusId;
            const campusName = e.target.dataset.campusName;
            document.getElementById("editCampusId").value = campusId;
            document.getElementById("editCampusName").value = campusName;
            window.dispatchEvent(new CustomEvent("open-modal", { detail: "editCampusModal" }));
        });
    });

    // Handle Edit Campus Form Submission
    const editCampusForm = document.getElementById("editCampusForm");
    editCampusForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const formData = new FormData(editCampusForm);

        try {
            const response = await fetch("/campuses/update", {
                method: "POST",
                headers: { "X-CSRF-Token": document.querySelector("meta[name='csrf-token']").content },
                body: formData,
            });
            const data = await response.json();
            updateCampusInTable(data.campus);
            window.dispatchEvent(new CustomEvent("close-modal", { detail: "editCampusModal" }));
        } catch (error) {
            console.error("Error updating campus:", error);
        }
    });

    function addCampusToTable(campus) {
        const tableBody = document.querySelector("table tbody");
        const newRow = document.createElement("tr");
        newRow.innerHTML = `
            <td class="py-2 px-4 border-b">${campus.campus_id}</td>
            <td class="py-2 px-4 border-b">${campus.campus_name}</td>
            <td class="py-2 px-4 border-b text-center">
                <button data-campus-id="${campus.campus_id}" data-campus-name="${campus.campus_name}" class="edit-campus-btn text-blue-500">Edit</button>
            </td>
        `;
        tableBody.appendChild(newRow);
    }

    function updateCampusInTable(campus) {
        const row = document.querySelector(`button[data-campus-id="${campus.campus_id}"]`).closest("tr");
        row.querySelector("td:nth-child(2)").textContent = campus.campus_name;
    }
});
