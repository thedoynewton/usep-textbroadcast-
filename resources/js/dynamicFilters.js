document.addEventListener("DOMContentLoaded", function () {
    const campusSelect = document.getElementById("campus");
    const academicUnitSelect = document.getElementById("academic_unit"); // For students (college)
    const programSelect = document.getElementById("program"); // For students
    const majorSelect = document.getElementById("major"); // For students
    const yearSelect = document.getElementById("year"); // For students (independent)

    const officeSelect = document.getElementById("office"); // For employees
    const typeSelect = document.getElementById("type"); // For employees
    const statusSelect = document.getElementById("status"); // For employees (independent)

    const totalRecipientsInput = document.getElementById("total_recipients"); // Total Recipients field
    const currentTab = document.querySelector('input[name="tab"]').value; // Identify current tab (all, students, or employees)

    // Function to dynamically update total recipients count for both students and employees
    function updateTotalRecipients(
        tab,
        campusId,
        collegeId = null,
        programId = null,
        majorId = null,
        yearId = null,
        officeId = null,
        typeId = null,
        statusId = null
    ) {
        if (tab === 'all' || tab === '') {
            // For the 'all' tab, update total recipients by getting counts for both students and employees
            let studentCountUrl = `/api/recipient-count?tab=students&campus=${campusId}`;
            let employeeCountUrl = `/api/recipient-count?tab=employees&campus=${campusId}`;

            if (collegeId) studentCountUrl += `&college=${collegeId}`;
            if (programId) studentCountUrl += `&program=${programId}`;
            if (majorId) studentCountUrl += `&major=${majorId}`;
            if (yearId) studentCountUrl += `&year=${yearId}`;

            if (officeId) employeeCountUrl += `&office=${officeId}`;
            if (typeId) employeeCountUrl += `&type=${typeId}`;
            if (statusId) employeeCountUrl += `&status=${statusId}`;

            // Fetch student count
            fetch(studentCountUrl)
                .then(response => response.json())
                .then(studentData => {
                    const studentCount = studentData.totalRecipients || 0;

                    // Fetch employee count
                    fetch(employeeCountUrl)
                        .then(response => response.json())
                        .then(employeeData => {
                            const employeeCount = employeeData.totalRecipients || 0;

                            // Combine student and employee counts
                            totalRecipientsInput.value = studentCount + employeeCount;
                        })
                        .catch(error => console.error("Error fetching employee count:", error));
                })
                .catch(error => console.error("Error fetching student count:", error));

        } else {
            // For specific tabs (students or employees), fetch the count for that tab
            let url = `/api/recipient-count?tab=${tab}&campus=${campusId}`;

            if (collegeId) url += `&college=${collegeId}`;
            if (programId) url += `&program=${programId}`;
            if (majorId) url += `&major=${majorId}`;
            if (yearId) url += `&year=${yearId}`;
            if (officeId) url += `&office=${officeId}`;
            if (typeId) url += `&type=${typeId}`;
            if (statusId) url += `&status=${statusId}`;

            fetch(url)
                .then((response) => response.json())
                .then((data) => {
                    totalRecipientsInput.value = data.totalRecipients;
                })
                .catch((error) =>
                    console.error("Error fetching recipient count:", error)
                );
        }
    }

    // Helper function to create a default "Select" option and an "All" option
    function createDefaultOption(text) {
        const option = document.createElement("option");
        option.value = "";
        option.text = `Select ${text}`;
        option.disabled = true;
        option.selected = true;
        return option;
    }

    function createAllOption(text) {
        const option = document.createElement("option");
        option.value = "all";
        option.text = `All ${text}`;
        return option;
    }

    // Update total recipients when the campus changes
    if (campusSelect) {
        campusSelect.addEventListener("change", function () {
            const campusId = this.value;
            updateTotalRecipients(currentTab, campusId); // Dynamically update recipients
        });
    }

    if (currentTab === "students") {
        // For Students: Fetch and update colleges when a campus is selected
        if (campusSelect) {
            campusSelect.addEventListener("change", function () {
                const campusId = this.value;

                // If "All Campuses" is selected, set all related dropdowns to "All"
                if (campusId === "all") {
                    academicUnitSelect.innerHTML = "";
                    academicUnitSelect.appendChild(
                        createAllOption("Academic Units")
                    );

                    programSelect.innerHTML = "";
                    programSelect.appendChild(createAllOption("Programs"));

                    majorSelect.innerHTML = "";
                    majorSelect.appendChild(createAllOption("Majors"));

                    updateTotalRecipients(
                        currentTab,
                        "all",
                        "all",
                        "all",
                        "all"
                    );
                    return;
                }

                // Clear and add default option to Academic Unit, Program, Major dropdowns
                academicUnitSelect.innerHTML = "";
                programSelect.innerHTML = "";
                majorSelect.innerHTML = "";

                academicUnitSelect.appendChild(
                    createDefaultOption("Academic Unit")
                );
                academicUnitSelect.appendChild(
                    createAllOption("Academic Units")
                );

                programSelect.appendChild(createDefaultOption("Program"));
                programSelect.appendChild(createAllOption("Programs"));

                majorSelect.appendChild(createDefaultOption("Major"));
                majorSelect.appendChild(createAllOption("Majors"));

                // Fetch colleges for the selected campus
                if (campusId) {
                    fetch(`/api/colleges/${campusId}`)
                        .then((response) => response.json())
                        .then((data) => {
                            data.forEach((college) => {
                                const option = document.createElement("option");
                                option.value = college.college_id;
                                option.text = college.college_name;
                                academicUnitSelect.appendChild(option);
                            });
                        })
                        .catch((error) =>
                            console.error("Error fetching colleges:", error)
                        );
                }
            });
        }

        // Fetch and update programs when a college is selected (For Students)
        if (academicUnitSelect) {
            academicUnitSelect.addEventListener("change", function () {
                const collegeId = this.value;
                const campusId = campusSelect.value;

                // If "All Academic Units" is selected, set Program and Major to "All"
                if (collegeId === "all") {
                    programSelect.innerHTML = "";
                    programSelect.appendChild(createAllOption("Programs"));

                    majorSelect.innerHTML = "";
                    majorSelect.appendChild(createAllOption("Majors"));

                    updateTotalRecipients(
                        currentTab,
                        campusId,
                        "all",
                        "all",
                        "all"
                    );
                    return;
                }

                // Clear and add default option to Program and Major dropdowns
                programSelect.innerHTML = "";
                majorSelect.innerHTML = "";

                programSelect.appendChild(createDefaultOption("Program"));
                programSelect.appendChild(createAllOption("Programs"));

                majorSelect.appendChild(createDefaultOption("Major"));
                majorSelect.appendChild(createAllOption("Majors"));

                // Dynamically update total recipients when a college is selected
                updateTotalRecipients(currentTab, campusId, collegeId);

                if (collegeId) {
                    fetch(`/api/programs/${collegeId}`)
                        .then((response) => response.json())
                        .then((data) => {
                            data.forEach((program) => {
                                const option = document.createElement("option");
                                option.value = program.program_id;
                                option.text = program.program_name;
                                programSelect.appendChild(option);
                            });
                        })
                        .catch((error) =>
                            console.error("Error fetching programs:", error)
                        );
                }
            });
        }

        // Fetch and update majors when a program is selected (For Students)
        if (programSelect) {
            programSelect.addEventListener("change", function () {
                const programId = this.value;
                const campusId = campusSelect.value;
                const collegeId = academicUnitSelect.value;

                // If "All Programs" is selected, set Major to "All"
                if (programId === "all") {
                    majorSelect.innerHTML = "";
                    majorSelect.appendChild(createAllOption("Majors"));

                    updateTotalRecipients(
                        currentTab,
                        campusId,
                        collegeId,
                        "all",
                        "all"
                    );
                    return;
                }

                // Clear and add default option to Major dropdown
                majorSelect.innerHTML = "";
                majorSelect.appendChild(createDefaultOption("Major"));
                majorSelect.appendChild(createAllOption("Majors"));

                // Dynamically update total recipients when a program is selected
                updateTotalRecipients(
                    currentTab,
                    campusId,
                    collegeId,
                    programId
                );

                if (programId) {
                    fetch(`/api/majors/${programId}`)
                        .then((response) => response.json())
                        .then((data) => {
                            data.forEach((major) => {
                                const option = document.createElement("option");
                                option.value = major.major_id;
                                option.text = major.major_name;
                                majorSelect.appendChild(option);
                            });
                        })
                        .catch((error) =>
                            console.error("Error fetching majors:", error)
                        );
                }
            });
        }

        // Update total recipients when a major is selected
        if (majorSelect) {
            majorSelect.addEventListener("change", function () {
                const majorId = this.value;
                const campusId = campusSelect.value;
                const collegeId = academicUnitSelect.value;
                const programId = programSelect.value;

                // Dynamically update total recipients when a major is selected
                updateTotalRecipients(
                    currentTab,
                    campusId,
                    collegeId,
                    programId,
                    majorId
                );
            });
        }

        // Update total recipients when a year is selected (Year independent)
        if (yearSelect) {
            yearSelect.addEventListener("change", function () {
                const yearId = this.value;
                const campusId = campusSelect.value;
                const collegeId = academicUnitSelect.value;
                const programId = programSelect.value;
                const majorId = majorSelect.value;

                // Dynamically update total recipients when a year is selected
                updateTotalRecipients(
                    currentTab,
                    campusId,
                    collegeId,
                    programId,
                    majorId,
                    yearId
                );
            });
        }
    }

    if (currentTab === "employees") {
        // For Employees: Fetch and update offices based on the selected campus
        if (campusSelect && officeSelect) {
            campusSelect.addEventListener("change", function () {
                const campusId = this.value;

                // Reset Office and Type dropdowns
                officeSelect.innerHTML = "";
                officeSelect.appendChild(createDefaultOption("Office"));
                officeSelect.appendChild(createAllOption("Offices"));

                typeSelect.innerHTML = "";
                typeSelect.appendChild(createDefaultOption("Type"));
                typeSelect.appendChild(createAllOption("Types"));

                if (campusId === "all") {
                    // Automatically select "All Offices," "All Types," and "All Statuses"
                    officeSelect.value = "all";
                    typeSelect.value = "all";

                    // Update total recipients dynamically for all campuses, offices, types, and statuses
                    updateTotalRecipients(
                        currentTab,
                        "all",
                        null,
                        null,
                        null,
                        null,
                        "all",
                        "all"
                    );
                    return;
                }

                // Fetch offices for the selected campus
                if (campusId) {
                    fetch(`/api/offices/${campusId}`)
                        .then((response) => response.json())
                        .then((data) => {
                            console.log("Fetched offices:", data);
                            if (data.length > 0) {
                                data.forEach((office) => {
                                    const option =
                                        document.createElement("option");
                                    option.value = office.office_id;
                                    option.text = office.office_name;
                                    officeSelect.appendChild(option);
                                });
                            } else {
                                // Handle case where no offices are found for the campus
                                const noDataOption =
                                    document.createElement("option");
                                noDataOption.value = "";
                                noDataOption.text = "No offices available";
                                noDataOption.disabled = true;
                                officeSelect.appendChild(noDataOption);
                            }
                        })
                        .catch((error) =>
                            console.error("Error fetching offices:", error)
                        );
                }

                // Dynamically update total recipients
                updateTotalRecipients(currentTab, campusId);
            });
        }

        // For Employees: Fetch and update types based on the selected office
        if (officeSelect) {
            officeSelect.addEventListener("change", function () {
                const officeId = this.value;
                const campusId = campusSelect.value;

                // Reset the Type dropdown
                typeSelect.innerHTML = "";
                typeSelect.appendChild(createDefaultOption("Type"));
                typeSelect.appendChild(createAllOption("Types"));

                if (officeId && officeId !== "all") {
                    // Fetch types linked to the selected office
                    fetch(`/api/types/${officeId}`)
                        .then((response) => {
                            if (!response.ok) {
                                throw new Error(
                                    `HTTP error! status: ${response.status}`
                                );
                            }
                            return response.json();
                        })
                        .then((data) => {
                            console.log("Fetched types:", data); // Debug the fetched data
                            if (data.length > 0) {
                                data.forEach((type) => {
                                    const option =
                                        document.createElement("option");
                                    option.value = type.type_id;
                                    option.text = type.type_name;
                                    typeSelect.appendChild(option);
                                });
                            } else {
                                const noDataOption =
                                    document.createElement("option");
                                noDataOption.value = "";
                                noDataOption.text = "No types available";
                                noDataOption.disabled = true;
                                typeSelect.appendChild(noDataOption);
                            }
                        })
                        .catch((error) => {
                            console.error("Error fetching types:", error);
                        });

                    // Dynamically update total recipients based on the selected office
                    updateTotalRecipients(
                        currentTab,
                        campusId,
                        null,
                        null,
                        null,
                        null,
                        officeId
                    );
                } else {
                    // If "All Offices" is selected:
                    // Automatically select "All Types" and "All Statuses"
                    typeSelect.value = "all";

                    // Update total recipients for all offices, types, and statuses
                    updateTotalRecipients(
                        currentTab,
                        campusId,
                        null,
                        null,
                        null,
                        null,
                        "all",
                        "all"
                    );
                }
            });
        }

        // Update total recipients when a type is selected
        if (typeSelect) {
            typeSelect.addEventListener("change", function () {
                const typeId = this.value;
                const campusId = campusSelect.value;
                const officeId = officeSelect.value;

                // Dynamically update total recipients when a type is selected
                updateTotalRecipients(
                    currentTab,
                    campusId,
                    null,
                    null,
                    null,
                    null,
                    officeId,
                    typeId
                );
            });
        }

        // Update total recipients when a status is selected (Status is independent)
        if (statusSelect) {
            statusSelect.addEventListener("change", function () {
                const statusId = this.value;
                const campusId = campusSelect.value;
                const officeId = officeSelect.value;
                const typeId = typeSelect.value;

                // Dynamically update total recipients when a status is selected
                updateTotalRecipients(
                    currentTab,
                    campusId,
                    null,
                    null,
                    null,
                    null,
                    officeId,
                    typeId,
                    statusId
                );
            });
        }
    }
});
