document.addEventListener('DOMContentLoaded', function () {
    const campusSelect = document.getElementById('campus');
    const academicUnitSelect = document.getElementById('academic_unit'); // For students
    const programSelect = document.getElementById('program'); // For students
    const majorSelect = document.getElementById('major'); // For students

    const officeSelect = document.getElementById('office'); // For employees
    const typeSelect = document.getElementById('type'); // For employees
    const statusSelect = document.getElementById('status'); // For employees

    // Check which tab is active and run code accordingly
    const currentTab = document.querySelector('input[name="tab"]').value; // Identify current tab (all, students, or employees)

    if (currentTab === 'students') {
        // For Students: Fetch and update colleges when a campus is selected
        if (campusSelect) {
            campusSelect.addEventListener('change', function () {
                const campusId = this.value;

                // Clear the Academic Unit, Program, and Major dropdowns
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

                // Clear the Program and Major dropdowns
                if (programSelect) programSelect.innerHTML = '<option>Select Program</option>';
                if (majorSelect) majorSelect.innerHTML = '<option>Select Major</option>';

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

                // Clear the Major dropdown
                if (majorSelect) majorSelect.innerHTML = '<option>Select Major</option>';

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
            });
        }

        // For Employees: Fetch and update types based on the selected office
        if (officeSelect) {  // Ensure officeSelect exists
            officeSelect.addEventListener('change', function () {
                const officeId = this.value;

                // Clear the Type dropdown
                if (typeSelect) typeSelect.innerHTML = '<option>Select Type</option>';

                if (officeId) {
                    fetch(`/api/types/${officeId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (typeSelect) {
                                data.forEach(type => {
                                    typeSelect.innerHTML += `<option value="${type.type_id}">${type.type_name}</option>`;
                                });
                            }
                        })
                        .catch(error => console.error('Error fetching types:', error));
                }
            });
        }
    }
});
