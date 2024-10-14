document.addEventListener('DOMContentLoaded', function () {
    const messageInput = document.getElementById('message');
    const charCountDisplay = document.getElementById('char-count');
    const templateDropdown = document.getElementById('template');
    const reviewButton = document.getElementById('open-review-modal');
    const closeModalButton = document.getElementById('close-review-modal');
    const reviewModal = document.getElementById('reviewModal');
    const previewMessage = document.getElementById('preview-message');
    
    const selectedCampus = document.getElementById('selected-campus');
    const selectedAcademicUnit = document.getElementById('selected-academic-unit');
    const selectedProgram = document.getElementById('selected-program');
    const selectedMajor = document.getElementById('selected-major');
    const selectedYear = document.getElementById('selected-year');
    const selectedOffice = document.getElementById('selected-office');
    const selectedType = document.getElementById('selected-type');
    const selectedStatus = document.getElementById('selected-status');
    const totalRecipients = document.getElementById('total-recipients');
    
    // Date and time elements
    const sendLaterRadio = document.getElementById('send_later');
    const sendNowRadio = document.getElementById('send_now');
    const sendDateInput = document.getElementById('send_date');
    const sendDateTimeDisplay = document.getElementById('selected-send-datetime');
    const sendDateTimeValue = document.getElementById('send-datetime');

    // Select elements
    const campusSelect = document.getElementById('campus');
    const academicUnitSelect = document.getElementById('academic_unit');
    const programSelect = document.getElementById('program');
    const majorSelect = document.getElementById('major');
    const yearSelect = document.getElementById('year');
    const officeSelect = document.getElementById('office');
    const typeSelect = document.getElementById('type');
    const statusSelect = document.getElementById('status');
    const totalRecipientsInput = document.getElementById('total_recipients');

    // Update character count when typing in the message field
    messageInput.addEventListener('input', function () {
        charCountDisplay.textContent = messageInput.value.length;
    });

    // Populate the message field when a template is selected
    templateDropdown.addEventListener('change', function () {
        const selectedTemplate = this.options[this.selectedIndex];
        const content = selectedTemplate.getAttribute('data-content');
        if (content) {
            messageInput.value = content;
            charCountDisplay.textContent = messageInput.value.length;
        }
    });

    // Show the review modal
    reviewButton.addEventListener('click', function (e) {
        e.preventDefault();

        // Populate the modal with the message and other selected details
        previewMessage.textContent = messageInput.value || 'No message entered';
        selectedCampus.textContent = campusSelect.options[campusSelect.selectedIndex].text || 'No campus selected';

        // Handle visibility for student options
        const isStudentTab = document.querySelector('input[name="tab"]').value === 'students';
        if (isStudentTab) {
            selectedAcademicUnit.textContent = academicUnitSelect.options[academicUnitSelect.selectedIndex]?.text || 'N/A';
            selectedProgram.textContent = programSelect.options[programSelect.selectedIndex]?.text || 'N/A';
            selectedMajor.textContent = majorSelect.options[majorSelect.selectedIndex]?.text || 'N/A';
            selectedYear.textContent = yearSelect.options[yearSelect.selectedIndex]?.text || 'N/A';
            document.getElementById('student-options').classList.remove('hidden');
            document.getElementById('employee-options').classList.add('hidden');
        }

        // Handle visibility for employee options
        const isEmployeeTab = document.querySelector('input[name="tab"]').value === 'employees';
        if (isEmployeeTab) {
            selectedOffice.textContent = officeSelect.options[officeSelect.selectedIndex]?.text || 'N/A';
            selectedType.textContent = typeSelect.options[typeSelect.selectedIndex]?.text || 'N/A';
            selectedStatus.textContent = statusSelect.options[statusSelect.selectedIndex]?.text || 'N/A';
            document.getElementById('employee-options').classList.remove('hidden');
            document.getElementById('student-options').classList.add('hidden');
        }

        // Update total recipients
        totalRecipients.textContent = totalRecipientsInput.value || '0';

        // Show date and time only if "Send Later" is selected
        if (sendLaterRadio.checked && sendDateInput.value) {
            sendDateTimeDisplay.classList.remove('hidden');
            sendDateTimeValue.textContent = new Date(sendDateInput.value).toLocaleString();
        } else {
            sendDateTimeDisplay.classList.add('hidden');
        }

        // Show the modal
        reviewModal.classList.remove('hidden');
    });

    // Hide the review modal
    closeModalButton.addEventListener('click', function () {
        reviewModal.classList.add('hidden');
    });

    // Handle the send message logic
    document.getElementById('confirm-send').addEventListener('click', function () {
        console.log("Message sent!");
        // You can add the form submission or AJAX logic here
        reviewModal.classList.add('hidden');
    });
});
