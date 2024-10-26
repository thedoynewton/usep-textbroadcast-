document.addEventListener('DOMContentLoaded', function() {
    const recipientTypeSelect = document.getElementById('recipient_type');
    const studentFields = document.getElementById('studentFields');
    const employeeFields = document.getElementById('employeeFields');
    const campusSelect = document.getElementById('campus');
    const academicUnitSelect = document.getElementById('academic_unit');
    const programSelect = document.getElementById('program'); // Targets the Program dropdown
    const majorSelect = document.getElementById('major'); // Targets the Major dropdown
    const yearSelect = document.getElementById('year'); // Targets the Year dropdown

    // Function to toggle fields based on recipient type
    function toggleRecipientFields() {
        const recipientType = recipientTypeSelect.value;

        if (recipientType === 'Student') {
            studentFields.classList.remove('hidden');
            employeeFields.classList.add('hidden');
        } else if (recipientType === 'Employee') {
            employeeFields.classList.remove('hidden');
            studentFields.classList.add('hidden');
        } else {
            studentFields.classList.add('hidden');
            employeeFields.classList.add('hidden');
        }
    }

    // Event listener for recipient type change
    recipientTypeSelect.addEventListener('change', toggleRecipientFields);
    toggleRecipientFields();

    // Function to populate academic units based on selected campus
    function populateAcademicUnits() {
        const campusId = campusSelect.value;
        academicUnitSelect.innerHTML = '<option value="">Select Academic Unit</option>';
        programSelect.innerHTML = '<option value="">Select Program</option>'; // Clear Program options when Academic Unit changes
        majorSelect.innerHTML = '<option value="">Select Major</option>'; // Clear Major options when Academic Unit changes

        if (campusId) {
            fetch(`/api/colleges/${campusId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(unit => {
                        academicUnitSelect.innerHTML += `<option value="${unit.college_id}">${unit.college_name}</option>`;
                    });
                })
                .catch(error => console.error('Error fetching academic units:', error));
        }
    }

    // Function to populate programs based on selected academic unit
    function populatePrograms() {
        const collegeId = academicUnitSelect.value;
        programSelect.innerHTML = '<option value="">Select Program</option>';
        majorSelect.innerHTML = '<option value="">Select Major</option>'; // Clear Major options when Program changes

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
    }

    // Function to populate majors based on selected program
    function populateMajors() {
        const programId = programSelect.value;
        majorSelect.innerHTML = '<option value="">Select Major</option>';

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
    }

    // Function to populate years on page load
    function populateYears() {
        yearSelect.innerHTML = '<option value="">Select Year</option>';
        fetch(`/api/years`)
            .then(response => response.json())
            .then(data => {
                data.forEach(year => {
                    yearSelect.innerHTML += `<option value="${year.year_id}">${year.year_name}</option>`;
                });
            })
            .catch(error => console.error('Error fetching years:', error));
    }

    // Event listeners for dropdown changes
    campusSelect.addEventListener('change', populateAcademicUnits); // Populate academic units on campus change
    academicUnitSelect.addEventListener('change', populatePrograms); // Populate programs on academic unit change
    programSelect.addEventListener('change', populateMajors); // Populate majors on program change

    // Populate years on page load
    populateYears();
});
