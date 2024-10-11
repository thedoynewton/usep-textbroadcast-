document.addEventListener('DOMContentLoaded', function () {
    const campusSelect = document.getElementById('campus');
    const academicUnitSelect = document.getElementById('academic_unit'); // For students (college)
    const programSelect = document.getElementById('program'); // For students
    const majorSelect = document.getElementById('major'); // For students
    const yearSelect = document.getElementById('year'); // For students (independent)

    const officeSelect = document.getElementById('office'); // For employees
    const typeSelect = document.getElementById('type'); // For employees
    const statusSelect = document.getElementById('status'); // For employees (independent)

    const totalRecipientsInput = document.getElementById('total_recipients'); // Total Recipients field
    const currentTab = document.querySelector('input[name="tab"]').value; // Identify current tab (all, students, or employees)

    // Function to dynamically update total recipients count for both students and employees
    function updateTotalRecipients(tab, campusId, collegeId = null, programId = null, majorId = null, yearId = null, officeId = null, typeId = null, statusId = null) {
        let url = `/api/recipient-count?tab=${tab}&campus=${campusId}`;
        
        if (collegeId) url += `&college=${collegeId}`;
        if (programId) url += `&program=${programId}`;
        if (majorId) url += `&major=${majorId}`;
        if (yearId) url += `&year=${yearId}`;
        if (officeId) url += `&office=${officeId}`;
        if (typeId) url += `&type=${typeId}`;
        if (statusId) url += `&status=${statusId}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                totalRecipientsInput.value = data.totalRecipients;
            })
            .catch(error => console.error('Error fetching recipient count:', error));
    }

    // Update total recipients when the campus changes
    if (campusSelect) {
        campusSelect.addEventListener('change', function () {
            const campusId = this.value;
            updateTotalRecipients(currentTab, campusId); // Dynamically update recipients
        });
    }

    // Update total recipients when switching tabs (All, Students, Employees)
    const tabs = document.querySelectorAll('a[href^="?tab="]');
    tabs.forEach(tab => {
        tab.addEventListener('click', function (event) {
            event.preventDefault();
            const selectedTab = this.href.split('tab=')[1];
            updateTotalRecipients(selectedTab, campusSelect.value, academicUnitSelect ? academicUnitSelect.value : null);
        });
    });

    if (currentTab === 'students') {
        // For Students: Fetch and update colleges when a campus is selected
        if (campusSelect) {
            campusSelect.addEventListener('change', function () {
                const campusId = this.value;

                // Clear the Academic Unit, Program, Major dropdowns
                if (academicUnitSelect) academicUnitSelect.innerHTML = '<option>Select Academic Unit</option>';
                if (programSelect) programSelect.innerHTML = '<option>Select Program</option>';
                if (majorSelect) majorSelect.innerHTML = '<option>Select Major</option>';

                // Fetch colleges for the selected campus
                if (campusId) {
                    fetch(`/api/colleges/${campusId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(college => {
                                if (academicUnitSelect) {
                                    academicUnitSelect.innerHTML += `<option value="${college.college_id}">${college.college_name}</option>`;
                                }
                            });
                        })
                        .catch(error => console.error('Error fetching colleges:', error));
                }
            });
        }

        // Fetch and update programs when a college is selected (For Students)
        if (academicUnitSelect) {
            academicUnitSelect.addEventListener('change', function () {
                const collegeId = this.value;
                const campusId = campusSelect.value;

                // Clear the Program, Major dropdowns
                if (programSelect) programSelect.innerHTML = '<option>Select Program</option>';
                if (majorSelect) majorSelect.innerHTML = '<option>Select Major</option>';

                // Dynamically update total recipients when a college is selected
                updateTotalRecipients(currentTab, campusId, collegeId);

                if (collegeId) {
                    fetch(`/api/programs/${collegeId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(program => {
                                if (programSelect) {
                                    programSelect.innerHTML += `<option value="${program.program_id}">${program.program_name}</option>`;
                                }
                            });
                        })
                        .catch(error => console.error('Error fetching programs:', error));
                }
            });
        }

        // Fetch and update majors when a program is selected (For Students)
        if (programSelect) {
            programSelect.addEventListener('change', function () {
                const programId = this.value;
                const campusId = campusSelect.value;
                const collegeId = academicUnitSelect.value;

                // Clear the Major dropdown
                if (majorSelect) majorSelect.innerHTML = '<option>Select Major</option>';

                // Dynamically update total recipients when a program is selected
                updateTotalRecipients(currentTab, campusId, collegeId, programId);

                if (programId) {
                    fetch(`/api/majors/${programId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(major => {
                                if (majorSelect) {
                                    majorSelect.innerHTML += `<option value="${major.major_id}">${major.major_name}</option>`;
                                }
                            });
                        })
                        .catch(error => console.error('Error fetching majors:', error));
                }
            });
        }

        // Update total recipients when a major is selected
        if (majorSelect) {
            majorSelect.addEventListener('change', function () {
                const majorId = this.value;
                const campusId = campusSelect.value;
                const collegeId = academicUnitSelect.value;
                const programId = programSelect.value;

                // Dynamically update total recipients when a major is selected
                updateTotalRecipients(currentTab, campusId, collegeId, programId, majorId);
            });
        }

        // Update total recipients when a year is selected (Year independent)
        if (yearSelect) {
            yearSelect.addEventListener('change', function () {
                const yearId = this.value;
                const campusId = campusSelect.value;
                const collegeId = academicUnitSelect.value;
                const programId = programSelect.value;
                const majorId = majorSelect.value;

                // Dynamically update total recipients when a year is selected
                updateTotalRecipients(currentTab, campusId, collegeId, programId, majorId, yearId);
            });
        }
    }

    if (currentTab === 'employees') {
        // For Employees: Fetch and update offices based on the selected campus
        if (campusSelect && officeSelect) {  // Ensure both campusSelect and officeSelect exist
            campusSelect.addEventListener('change', function () {
                const campusId = this.value;

                // Clear the Office and Type dropdowns
                if (officeSelect) officeSelect.innerHTML = '<option>Select Office</option>';
                if (typeSelect) typeSelect.innerHTML = '<option>Select Type</option>';

                // Fetch offices for the selected campus
                if (campusId) {
                    fetch(`/api/offices/${campusId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (officeSelect) {
                                data.forEach(office => {
                                    officeSelect.innerHTML += `<option value="${office.office_id}">${office.office_name}</option>`;
                                });
                            }
                        })
                        .catch(error => console.error('Error fetching offices:', error));
                }

                // Dynamically update total recipients when a campus is selected (for Employees)
                updateTotalRecipients(currentTab, campusId);
            });
        }

        // For Employees: Fetch and update types based on the selected office
        if (officeSelect) {  // Ensure officeSelect exists
            officeSelect.addEventListener('change', function () {
                const officeId = this.value;
                const campusId = campusSelect.value;

                // Clear the Type dropdown
                if (typeSelect) typeSelect.innerHTML = '<option>Select Type</option>';

                // Dynamically update total recipients when an office is selected
                updateTotalRecipients(currentTab, campusId, null, null, null, null, officeId);

                if (officeId) {
                    fetch(`/api/types/${officeId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(type => {
                                if (typeSelect) {
                                    typeSelect.innerHTML += `<option value="${type.type_id}">${type.type_name}</option>`;
                                }
                            });
                        })
                        .catch(error => console.error('Error fetching types:', error));
                }
            });
        }

        // Update total recipients when a type is selected
        if (typeSelect) {
            typeSelect.addEventListener('change', function () {
                const typeId = this.value;
                const campusId = campusSelect.value;
                const officeId = officeSelect.value;

                // Dynamically update total recipients when a type is selected
                updateTotalRecipients(currentTab, campusId, null, null, null, null, officeId, typeId);
            });
        }

        // Update total recipients when a status is selected (Status is independent)
        if (statusSelect) {
            statusSelect.addEventListener('change', function () {
                const statusId = this.value;
                const campusId = campusSelect.value;
                const officeId = officeSelect.value;
                const typeId = typeSelect.value;

                // Dynamically update total recipients when a status is selected
                updateTotalRecipients(currentTab, campusId, null, null, null, null, officeId, typeId, statusId);
            });
        }
    }
});
