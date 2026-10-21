/**
 * practical13.js - Practical 13: Fetch API AJAX CRUD Implementation
 * Performs List, Add, Update, and Delete operations without page reload.
 */

// API endpoint URL
const API_URL = "api.php";

// DOM Elements
const studentForm    = document.getElementById("studentForm");
const formTitle      = document.getElementById("formTitle");
const studentIdInput = document.getElementById("student_id");
const nameInput      = document.getElementById("name");
const emailInput     = document.getElementById("email");
const courseInput    = document.getElementById("course");
const semesterInput  = document.getElementById("semester");
const submitBtn      = document.getElementById("submitBtn");
const cancelBtn      = document.getElementById("cancelBtn");
const tableBody      = document.getElementById("studentsTableBody");
const loadingState   = document.getElementById("loadingState");
const emptyState     = document.getElementById("emptyState");
const alertContainer = document.getElementById("alertContainer");
const refreshBtn     = document.getElementById("refreshBtn");

// Helper function to escape HTML and prevent XSS in DOM
function escapeHtml(text) {
    if (text === null || text === undefined) return "";
    return String(text)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Display feedback message banner (success or error)
function showAlert(type, message) {
    const alertClass = type === "success" ? "alert-success" : "alert-error";
    const icon = type === "success" ? "&#10003;" : "&#9888;";

    alertContainer.innerHTML = `
        <div class="alert ${alertClass}">
            <div>
                <strong>${icon}</strong> ${escapeHtml(message)}
            </div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove();">&times;</button>
        </div>
    `;

    // Automatically remove alert after 5 seconds
    setTimeout(() => {
        const alert = alertContainer.querySelector(".alert");
        if (alert) alert.remove();
    }, 5000);
}

// =============================================================
// 1. LIST: Fetch all students from API (GET request)
// =============================================================
async function fetchStudents() {
    loadingState.classList.remove("hidden");
    emptyState.classList.add("hidden");
    tableBody.innerHTML = "";

    try {
        const response = await fetch(API_URL, {
            method: "GET",
            headers: { "Accept": "application/json" }
        });

        const result = await response.json();

        loadingState.classList.add("hidden");

        if (response.ok && result.status === "success") {
            const students = result.data || [];

            if (students.length === 0) {
                emptyState.classList.remove("hidden");
                return;
            }

            // Render student table rows
            tableBody.innerHTML = students.map((student, index) => `
                <tr id="student-row-${student.id}">
                    <td>${index + 1}</td>
                    <td><strong>${escapeHtml(student.name)}</strong></td>
                    <td>${escapeHtml(student.email)}</td>
                    <td><span class="badge">${escapeHtml(student.course)}</span></td>
                    <td>Sem ${escapeHtml(student.semester)}</td>
                    <td>
                        <div class="action-buttons">
                            <button
                                type="button"
                                class="btn btn-sm btn-edit"
                                onclick="startEditStudent(${student.id}, '${escapeHtml(student.name)}', '${escapeHtml(student.email)}', '${escapeHtml(student.course)}', ${student.semester})"
                            >Edit</button>
                            <button
                                type="button"
                                class="btn btn-sm btn-delete"
                                onclick="deleteStudent(${student.id}, '${escapeHtml(student.name)}')"
                            >Delete</button>
                        </div>
                    </td>
                </tr>
            `).join("");

        } else {
            showAlert("error", result.message || "Failed to load students.");
        }
    } catch (error) {
        loadingState.classList.add("hidden");
        showAlert("error", "Network error while connecting to the API: " + error.message);
    }
}

// =============================================================
// 2. ADD & UPDATE: Handle Form Submission (POST or PUT request)
// =============================================================
studentForm.addEventListener("submit", async function (e) {
    // Prevent the default browser form submission (prevents page reload)
    e.preventDefault();

    const id = studentIdInput.value.trim();
    const isEditing = id !== "";

    const payload = {
        name: nameInput.value.trim(),
        email: emailInput.value.trim(),
        course: courseInput.value.trim(),
        semester: parseInt(semesterInput.value.trim(), 10)
    };

    if (isEditing) {
        payload.id = parseInt(id, 10);
    }

    // Determine HTTP Method: PUT for update, POST for add
    const method = isEditing ? "PUT" : "POST";
    const url    = isEditing ? `${API_URL}?id=${id}` : API_URL;

    submitBtn.disabled = true;
    submitBtn.textContent = isEditing ? "Updating..." : "Adding...";

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (response.ok && result.status === "success") {
            showAlert("success", result.message);
            resetForm();
            // Refresh table asynchronously without reloading page
            fetchStudents();
        } else {
            showAlert("error", result.message || "An error occurred.");
        }
    } catch (error) {
        showAlert("error", "Network error while saving student: " + error.message);
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = isEditing ? "Update Student" : "Add Student";
    }
});

// =============================================================
// 3. EDIT: Populate Form to Enter Edit Mode
// =============================================================
function startEditStudent(id, name, email, course, semester) {
    studentIdInput.value = id;
    nameInput.value      = name;
    emailInput.value     = email;
    courseInput.value    = course;
    semesterInput.value  = semester;

    formTitle.textContent = `Edit Student (ID: ${id})`;
    submitBtn.textContent = "Update Student";
    cancelBtn.classList.remove("hidden");

    // Scroll form into view smoothly
    studentForm.scrollIntoView({ behavior: "smooth", block: "start" });
    nameInput.focus();
}

// Cancel edit and return form to default "Add" mode
function resetForm() {
    studentForm.reset();
    studentIdInput.value = "";
    formTitle.textContent = "Add New Student";
    submitBtn.textContent = "Add Student";
    cancelBtn.classList.add("hidden");
}

cancelBtn.addEventListener("click", resetForm);

// =============================================================
// 4. DELETE: Remove student after confirmation (DELETE request)
// =============================================================
async function deleteStudent(id, name) {
    const confirmed = confirm(`Are you sure you want to delete student "${name}"?`);
    if (!confirmed) {
        return;
    }

    try {
        const response = await fetch(`${API_URL}?id=${id}`, {
            method: "DELETE",
            headers: {
                "Accept": "application/json"
            }
        });

        const result = await response.json();

        if (response.ok && result.status === "success") {
            showAlert("success", result.message);

            // If the student being deleted was loaded in the edit form, reset it
            if (studentIdInput.value === String(id)) {
                resetForm();
            }

            // Refresh table asynchronously
            fetchStudents();
        } else {
            showAlert("error", result.message || "Failed to delete student.");
        }
    } catch (error) {
        showAlert("error", "Network error while deleting student: " + error.message);
    }
}

// Manual refresh button click listener
refreshBtn.addEventListener("click", fetchStudents);

// Initial load: Fetch students on page load
document.addEventListener("DOMContentLoaded", fetchStudents);
