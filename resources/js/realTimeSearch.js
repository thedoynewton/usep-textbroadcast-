// realTimeSearch.js
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("searchInput");
    const studentTableBody = document.getElementById("studentTableBody");
    const employeeTableBody = document.getElementById("employeeTableBody");

    function debounce(func, delay) {
        let debounceTimer;
        return function (...args) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => func.apply(this, args), delay);
        };
    }

    function fetchSearchResults() {
        const searchQuery = searchInput.value;

        fetch(`/app-management/search?search=${searchQuery}`, {
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                updateResults(data.students, data.employees, searchQuery);
            })
            .catch((error) => console.error("Error fetching search results:", error));
    }

    // Helper function to highlight matched text
    function highlightText(text, searchTerm) {
        if (!searchTerm) return text; // Return original text if no search term
        const regex = new RegExp(`(${searchTerm})`, "gi");
        return text.replace(regex, `<span style="background-color: yellow;">$1</span>`);
    }

    function updateResults(students, employees, searchTerm) {
        studentTableBody.innerHTML = "";
        employeeTableBody.innerHTML = "";

        if (!Array.isArray(students) || !Array.isArray(employees)) {
            console.error("Invalid data format:", { students, employees });
            return;
        }

        // Populate students
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

        // Populate employees
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

    searchInput.addEventListener("input", debounce(fetchSearchResults, 300));
});
