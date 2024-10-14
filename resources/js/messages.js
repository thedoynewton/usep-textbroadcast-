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
    
    const sendLaterRadio = document.getElementById('send_later');
    const sendNowRadio = document.getElementById('send_now');
    const sendDateInput = document.getElementById('send_date');
    const sendDateTimeDisplay = document.getElementById('selected-send-datetime');
    const sendDateTimeValue = document.getElementById('send-datetime');

    const campusSelect = document.getElementById('campus');
    const academicUnitSelect = document.getElementById('academic_unit');
    const programSelect = document.getElementById('program');
    const majorSelect = document.getElementById('major');
    const yearSelect = document.getElementById('year');
    const officeSelect = document.getElementById('office');
    const typeSelect = document.getElementById('type');
    const statusSelect = document.getElementById('status');
    const totalRecipientsInput = document.getElementById('total_recipients');

    // Error message elements
    const errorMessageElements = {
        campus: document.createElement('div'),
        academic_unit: document.createElement('div'),
        program: document.createElement('div'),
        major: document.createElement('div'),
        year: document.createElement('div'),
        office: document.createElement('div'),
        type: document.createElement('div'),
        status: document.createElement('div'),
        message: document.createElement('div'),
        totalRecipients: document.createElement('div')
    };

    function displayError(field, message) {
        errorMessageElements[field].textContent = message;
        errorMessageElements[field].classList.add('text-red-500', 'text-sm');
        document.getElementById(field).after(errorMessageElements[field]);
    }

    function clearErrors() {
        Object.keys(errorMessageElements).forEach(field => {
            errorMessageElements[field].textContent = '';
        });
    }

    // Validation function
    function validateForm() {
        let isValid = true;
        clearErrors();

        if (!campusSelect.value) {
            displayError('campus', 'Please select a campus.');
            isValid = false;
        }

        if (document.querySelector('input[name="tab"]').value === 'students') {
            if (!academicUnitSelect.value) {
                displayError('academic_unit', 'Please select an academic unit.');
                isValid = false;
            }
            if (!programSelect.value) {
                displayError('program', 'Please select a program.');
                isValid = false;
            }
            if (!majorSelect.value) {
                displayError('major', 'Please select a major.');
                isValid = false;
            }
            if (!yearSelect.value) {
                displayError('year', 'Please select a year.');
                isValid = false;
            }
        }

        if (document.querySelector('input[name="tab"]').value === 'employees') {
            if (!officeSelect.value) {
                displayError('office', 'Please select an office.');
                isValid = false;
            }
            if (!typeSelect.value) {
                displayError('type', 'Please select a type.');
                isValid = false;
            }
            if (!statusSelect.value) {
                displayError('status', 'Please select a status.');
                isValid = false;
            }
        }

        if (messageInput.value.length === 0 || messageInput.value.length > 160) {
            displayError('message', 'Please enter a message.');
            isValid = false;
        }

        if (totalRecipientsInput.value == 0) {
            displayError('totalRecipients', 'No recipients counted.');
            isValid = false;
        }

        return isValid;
    }

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

        // Perform validation
        if (!validateForm()) {
            return; // If validation fails, don't show the modal
        }

        // Populate the modal with the message and other selected details
        previewMessage.textContent = messageInput.value || 'No message entered';
        selectedCampus.textContent = campusSelect.options[campusSelect.selectedIndex].text || 'No campus selected';

        const isStudentTab = document.querySelector('input[name="tab"]').value === 'students';
        if (isStudentTab) {
            selectedAcademicUnit.textContent = academicUnitSelect.options[academicUnitSelect.selectedIndex]?.text || 'N/A';
            selectedProgram.textContent = programSelect.options[programSelect.selectedIndex]?.text || 'N/A';
            selectedMajor.textContent = majorSelect.options[majorSelect.selectedIndex]?.text || 'N/A';
            selectedYear.textContent = yearSelect.options[yearSelect.selectedIndex]?.text || 'N/A';
            document.getElementById('student-options').classList.remove('hidden');
            document.getElementById('employee-options').classList.add('hidden');
        }

        const isEmployeeTab = document.querySelector('input[name="tab"]').value === 'employees';
        if (isEmployeeTab) {
            selectedOffice.textContent = officeSelect.options[officeSelect.selectedIndex]?.text || 'N/A';
            selectedType.textContent = typeSelect.options[typeSelect.selectedIndex]?.text || 'N/A';
            selectedStatus.textContent = statusSelect.options[statusSelect.selectedIndex]?.text || 'N/A';
            document.getElementById('employee-options').classList.remove('hidden');
            document.getElementById('student-options').classList.add('hidden');
        }

        totalRecipients.textContent = totalRecipientsInput.value || '0';

        if (sendLaterRadio.checked && sendDateInput.value) {
            sendDateTimeDisplay.classList.remove('hidden');
            sendDateTimeValue.textContent = new Date(sendDateInput.value).toLocaleString();
        } else {
            sendDateTimeDisplay.classList.add('hidden');
        }

        reviewModal.classList.remove('hidden');
    });

    closeModalButton.addEventListener('click', function () {
        reviewModal.classList.add('hidden');
    });

    document.getElementById('confirm-send').addEventListener('click', function () {
        console.log("Message sent!");
        reviewModal.classList.add('hidden');
    });
});
