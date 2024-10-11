document.addEventListener('DOMContentLoaded', function () {
    const campusSelect = document.getElementById('campus');
    const academicUnitSelect = document.getElementById('academic_unit');
    const programSelect = document.getElementById('program');
    const majorSelect = document.getElementById('major');

    // Ensure the element exists before trying to add event listeners
    if (campusSelect) {
        // Fetch and update colleges when a campus is selected
        campusSelect.addEventListener('change', function () {
            const campusId = this.value;

            // Clear the Academic Unit, Program, and Major dropdowns
            academicUnitSelect.innerHTML = '<option>Select Academic Unit</option>';
            programSelect.innerHTML = '<option>Select Program</option>';
            majorSelect.innerHTML = '<option>Select Major</option>';

            // Fetch colleges for the selected campus
            if (campusId) {
                fetch(`/api/colleges/${campusId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(college => {
                            academicUnitSelect.innerHTML += `<option value="${college.college_id}">${college.college_name}</option>`;
                        });
                    })
                    .catch(error => console.error('Error fetching colleges:', error));
            }
        });
    }

    if (academicUnitSelect) {
        // When Academic Unit (College) changes, fetch and populate Programs
        academicUnitSelect.addEventListener('change', function () {
            const collegeId = this.value;

            // Clear the Program and Major dropdowns
            programSelect.innerHTML = '<option>Select Program</option>';
            majorSelect.innerHTML = '<option>Select Major</option>';

            if (collegeId) {
                fetch(`/api/programs/${collegeId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(program => {
                            programSelect.innerHTML += `<option value="${program.program_id}">${program.program_name}</option>`;
                        });
                    })
                    .catch(error => console.error('Error fetching programs:', error));
            }
        });
    }

    if (programSelect) {
        // When Program changes, fetch and populate Majors
        programSelect.addEventListener('change', function () {
            const programId = this.value;

            // Clear the Major dropdown
            majorSelect.innerHTML = '<option>Select Major</option>';

            if (programId) {
                fetch(`/api/majors/${programId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(major => {
                            majorSelect.innerHTML += `<option value="${major.major_id}">${major.major_name}</option>`;
                        });
                    })
                    .catch(error => console.error('Error fetching majors:', error));
            }
        });
    }
});
