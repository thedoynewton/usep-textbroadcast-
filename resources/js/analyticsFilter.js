// resources/js/analyticsFilter.js

document.addEventListener('DOMContentLoaded', function() {
    const recipientTypeSelect = document.getElementById('recipient_type');
    const studentFields = document.getElementById('studentFields');
    const employeeFields = document.getElementById('employeeFields');

    function toggleRecipientFields() {
        const recipientType = recipientTypeSelect.value;

        // Show additional fields only when "Student" or "Employee" is selected
        if (recipientType === 'Student') {
            studentFields.classList.remove('hidden');
            employeeFields.classList.add('hidden');
        } else if (recipientType === 'Employee') {
            employeeFields.classList.remove('hidden');
            studentFields.classList.add('hidden');
        } else {
            // Hide both fields if "All" is selected
            studentFields.classList.add('hidden');
            employeeFields.classList.add('hidden');
        }
    }

    recipientTypeSelect.addEventListener('change', toggleRecipientFields);

    // Initial call to set the correct fields based on the current selection
    toggleRecipientFields();
});
