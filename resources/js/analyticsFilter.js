document.addEventListener("DOMContentLoaded", function () {
    const recipientTypeSelect = document.getElementById("recipient_type");
    const studentFields = document.getElementById("studentFields");
    const employeeFields = document.getElementById("employeeFields");
    const campusSelect = document.getElementById("campus");
    const academicUnitSelect = document.getElementById("academic_unit");
    const programSelect = document.getElementById("program");
    const majorSelect = document.getElementById("major");
    const yearSelect = document.getElementById("year");
    const officeSelect = document.getElementById("office");
    const typeSelect = document.getElementById("type");
    const statusSelect = document.getElementById("status");

    // Function to toggle fields based on recipient type
    function toggleRecipientFields() {
        const recipientType = recipientTypeSelect.value;

        if (recipientType === "Student") {
            studentFields.classList.remove("hidden");
            employeeFields.classList.add("hidden");
        } else if (recipientType === "Employee") {
            employeeFields.classList.remove("hidden");
            studentFields.classList.add("hidden");
            populateOffices(); // Populate employee-specific dropdowns when Employee is selected
        } else {
            studentFields.classList.add("hidden");
            employeeFields.classList.add("hidden");
        }
    }

    recipientTypeSelect.addEventListener("change", toggleRecipientFields);
    toggleRecipientFields();

    // Function to populate academic units based on selected campus
    function populateAcademicUnits() {
        const campusId = campusSelect.value;
        academicUnitSelect.innerHTML =
            '<option value="">Select Academic Unit</option>';
        programSelect.innerHTML = '<option value="">Select Program</option>';
        majorSelect.innerHTML = '<option value="">Select Major</option>';

        if (campusId) {
            fetch(`/api/colleges/${campusId}`)
                .then((response) => response.json())
                .then((data) => {
                    data.forEach((unit) => {
                        academicUnitSelect.innerHTML += `<option value="${unit.college_id}">${unit.college_name}</option>`;
                    });
                })
                .catch((error) =>
                    console.error("Error fetching academic units:", error)
                );
        }
    }

    // Function to populate programs based on selected academic unit
    function populatePrograms() {
        const collegeId = academicUnitSelect.value;
        programSelect.innerHTML = '<option value="">Select Program</option>';
        majorSelect.innerHTML = '<option value="">Select Major</option>';

        if (collegeId) {
            fetch(`/api/programs/${collegeId}`)
                .then((response) => response.json())
                .then((data) => {
                    data.forEach((program) => {
                        programSelect.innerHTML += `<option value="${program.program_id}">${program.program_name}</option>`;
                    });
                })
                .catch((error) =>
                    console.error("Error fetching programs:", error)
                );
        }
    }

    // Function to populate majors based on selected program
    function populateMajors() {
        const programId = programSelect.value;
        majorSelect.innerHTML = '<option value="">Select Major</option>';

        if (programId) {
            fetch(`/api/majors/${programId}`)
                .then((response) => response.json())
                .then((data) => {
                    data.forEach((major) => {
                        majorSelect.innerHTML += `<option value="${major.major_id}">${major.major_name}</option>`;
                    });
                })
                .catch((error) =>
                    console.error("Error fetching majors:", error)
                );
        }
    }

    // Function to populate years on page load
    function populateYears() {
        yearSelect.innerHTML = '<option value="">Select Year</option>';
        fetch(`/api/years`)
            .then((response) => response.json())
            .then((data) => {
                data.forEach((year) => {
                    yearSelect.innerHTML += `<option value="${year.year_id}">${year.year_name}</option>`;
                });
            })
            .catch((error) => console.error("Error fetching years:", error));
    }

    // Function to populate offices based on selected campus
    function populateOffices() {
        const campusId = campusSelect.value;
        officeSelect.innerHTML = '<option value="">Select Office</option>';
        typeSelect.innerHTML = '<option value="">Select Type</option>'; // Clear type options when office changes

        if (campusId) {
            fetch(`/api/offices/${campusId}`)
                .then((response) => response.json())
                .then((data) => {
                    data.forEach((office) => {
                        officeSelect.innerHTML += `<option value="${office.office_id}">${office.office_name}</option>`;
                    });
                })
                .catch((error) =>
                    console.error("Error fetching offices:", error)
                );
        }
    }

    // Function to populate types based on selected office
    function populateTypes() {
        const officeId = officeSelect.value;
        typeSelect.innerHTML = '<option value="">Select Type</option>';

        if (officeId) {
            fetch(`/api/types/${officeId}`)
                .then((response) => response.json())
                .then((data) => {
                    data.forEach((type) => {
                        typeSelect.innerHTML += `<option value="${type.type_id}">${type.type_name}</option>`;
                    });
                })
                .catch((error) =>
                    console.error("Error fetching types:", error)
                );
        }
    }

    // Function to populate status options
    function populateStatus() {
        statusSelect.innerHTML = '<option value="">Select Status</option>';

        fetch(`/api/statuses`) // Replace with the actual endpoint for statuses if different
            .then((response) => response.json())
            .then((data) => {
                data.forEach((status) => {
                    statusSelect.innerHTML += `<option value="${status.status_id}">${status.status_name}</option>`;
                });
            })
            .catch((error) => console.error("Error fetching statuses:", error));
    }

    // Event listeners for dropdown changes
    campusSelect.addEventListener("change", populateAcademicUnits);
    academicUnitSelect.addEventListener("change", populatePrograms);
    programSelect.addEventListener("change", populateMajors);
    campusSelect.addEventListener("change", populateOffices); // Populate offices on campus change
    officeSelect.addEventListener("change", populateTypes); // Populate types on office change

    // Populate years on page load
    populateYears();
    populateStatus();
});
