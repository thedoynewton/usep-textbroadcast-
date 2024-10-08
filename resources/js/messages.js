document.addEventListener('DOMContentLoaded', function () {
    const messageInput = document.getElementById('message');
    const charCountDisplay = document.getElementById('char-count');
    const templateDropdown = document.getElementById('template');

    // Update character count when typing in the message field
    messageInput.addEventListener('input', function () {
        charCountDisplay.textContent = messageInput.value.length;
    });

    // Populate the message field when a template is selected and update character count
    templateDropdown.addEventListener('change', function () {
        const selectedTemplate = this.options[this.selectedIndex];
        const content = selectedTemplate.getAttribute('data-content');
        messageInput.value = content || '';
        charCountDisplay.textContent = messageInput.value.length;  // Update character count
    });
});