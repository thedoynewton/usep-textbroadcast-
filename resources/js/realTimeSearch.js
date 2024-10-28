// realTimeSearch.js
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("searchInput");
    const campusFilter = document.getElementById("campusFilter");
    const filterForm = document.getElementById("filterForm");
    const filterButton = document.getElementById("filterButton"); // Updated to use id selector
    const studentTableBody = document.getElementById("studentTableBody");
    const employeeTableBody = document.getElementById("employeeTableBody");

    // Prevent the form from submitting traditionally
    if (filterForm) {
        filterForm.addEventListener("submit", (event) => {
            event.preventDefault(); // Prevent traditional form submission
        });
    }

    // Event listener for the "Filter" button to trigger AJAX filter
    if (filterButton) {
        filterButton.addEventListener("click", fetchFilteredResults);
    }

    // Debounced search input listener if searchInput exists
    if (searchInput) {
        searchInput.addEventListener("input", debounce(fetchFilteredResults, 300));
    }

    // Fetch and update table with filtered results
    function fetchFilteredResults() {
        const searchQuery = searchInput ? searchInput.value : '';
        const campusId = campusFilter ? campusFilter.value : '';

        fetch(`/app-management/search?search=${searchQuery}&campus_id=${campusId}`, {
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                updateResults(data.students, data.employees, searchQuery);
            })
            .catch((error) => console.error("Error fetching filtered results:", error));
    }

    // Update results in the table
    function updateResults(students, employees, searchTerm) {
        studentTableBody.innerHTML = "";
        employeeTableBody.innerHTML = "";

        if (!Array.isArray(students) || !Array.isArray(employees)) {
            console.error("Invalid data format:", { students, employees });
            return;
        }

        students.forEach((student) => {
            studentTableBody.innerHTML += `
                <tr class="bg-white">
                    <td class="border dark:border-gray-700 px-4 py-2">${highlightText(student.stud_fname + ' ' + student.stud_lname, searchTerm)}</td>
                    <td class="border dark:border-gray-700 px-4 py-2">${highlightText(student.stud_email, searchTerm)}</td>
                    <td class="border dark:border-gray-700 px-4 py-2">${highlightText(student.stud_contact, searchTerm)}</td>
                    <td class="border dark:border-gray-700 px-4 py-2">${student.campus ? highlightText(student.campus.campus_name, searchTerm) : 'N/A'}</td>
                </tr>
            `;
        });

        employees.forEach((employee) => {
            employeeTableBody.innerHTML += `
                <tr class="bg-white">
                    <td class="border dark:border-gray-700 px-4 py-2">${highlightText(employee.emp_fname + ' ' + employee.emp_lname, searchTerm)}</td>
                    <td class="border dark:border-gray-700 px-4 py-2">${highlightText(employee.emp_email, searchTerm)}</td>
                    <td class="border dark:border-gray-700 px-4 py-2">${highlightText(employee.emp_contact, searchTerm)}</td>
                    <td class="border dark:border-gray-700 px-4 py-2">${employee.campus ? highlightText(employee.campus.campus_name, searchTerm) : 'N/A'}</td>
                </tr>
            `;
        });
    }

    // Helper function to highlight matched text
    function highlightText(text, searchTerm) {
        if (!searchTerm) return text;
        const regex = new RegExp(`(${searchTerm})`, "gi");
        return text.replace(regex, `<span style="background-color: yellow;">$1</span>`);
    }

    // Debounce function to limit rate of search calls
    function debounce(func, delay) {
        let debounceTimer;
        return function (...args) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => func.apply(this, args), delay);
        };
    }
});
