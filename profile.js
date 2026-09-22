// =====================================================
// SIDEBAR
// =====================================================

function toggleMenu() {

    const sidebar = document.getElementById("sidebar");

    if (sidebar.style.left == "0px") {
        sidebar.style.left = "-300px";
    }
    else {
        sidebar.style.left = "0px";
    }
}


// =====================================================
// DARK MODE
// =====================================================

const toggle = document.getElementById("theme-toggle");

// Apply saved theme when page opens
if (localStorage.getItem("darkMode") === "enabled") {

    document.body.classList.add("dark-mode");

    if (toggle) {
        toggle.textContent = "☀️";
    }
}


// Change theme
if (toggle) {

    toggle.addEventListener("click", function () {

        document.body.classList.toggle("dark-mode");

        if (document.body.classList.contains("dark-mode")) {

            localStorage.setItem("darkMode", "enabled");

            toggle.textContent = "☀️";
        }
        else {

            localStorage.setItem("darkMode", "disabled");

            toggle.textContent = "🌙";
        }

    });
}


// =====================================================
// LOAD PROFILE DATA FROM JSON
// =====================================================

function loadJSONProfile() {

    fetch("profile.json")

        .then(response => {

            if (!response.ok) {
                throw new Error("Could not load profile.json");
            }

            return response.json();
        })

        .then(data => {

            // Put JSON data into input fields

            document.getElementById("name").value = data.name;

            document.getElementById("email").value = data.email;

            document.getElementById("phone").value = data.phone;

            document.getElementById("enrollment").value =
                data.enrollment;

            document.getElementById("branch").value =
                data.branch;

            document.getElementById("year").value =
                data.year;

            document.getElementById("college").value =
                data.college;

            document.getElementById("cgpa").value =
                data.cgpa;


            // Update profile display

            document.getElementById("displayName").textContent =
                data.name;

            document.getElementById("displayCGPA").textContent =
                data.cgpa;

            document.getElementById("displayYear").textContent =
                data.year;


            console.log("Profile loaded from JSON successfully.");

        })

        .catch(error => {

            console.error("Error loading JSON:", error);

        });
}


// =====================================================
// EDIT PROFILE
// =====================================================

function enableEditing() {

    document.getElementById("name").disabled = false;

    document.getElementById("email").disabled = false;

    document.getElementById("phone").disabled = false;

    document.getElementById("enrollment").disabled = false;

    document.getElementById("branch").disabled = false;

    document.getElementById("year").disabled = false;

    document.getElementById("college").disabled = false;

    document.getElementById("cgpa").disabled = false;


    // Enable submit button

    document.getElementById("submitBtn").disabled = false;


    // Change edit button text

    document.getElementById("editBtn").textContent =
        "✏️ Editing...";
}


// =====================================================
// SUBMIT PROFILE
// =====================================================

function submitProfile() {

    // Get values from HTML

    let name =
        document.getElementById("name").value.trim();

    let email =
        document.getElementById("email").value.trim();

    let phone =
        document.getElementById("phone").value.trim();

    let enrollment =
        document.getElementById("enrollment").value.trim();

    let branch =
        document.getElementById("branch").value;

    let year =
        document.getElementById("year").value;

    let college =
        document.getElementById("college").value.trim();

    let cgpa =
        document.getElementById("cgpa").value;


    // =================================================
    // REGEX PATTERNS
    // =================================================

    let nameRegex =
        /^[A-Za-z ]{3,50}$/;

    let emailRegex =
        /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;

    let phoneRegex =
        /^\+91 [6-9][0-9]{4} [0-9]{5}$/;

    let enrollmentRegex =
        /^[A-Za-z0-9]{5,20}$/;

    let collegeRegex =
        /^[A-Za-z ]{2,50}$/;


    // =================================================
    // EMPTY VALIDATION
    // =================================================

    if (
        name === "" ||
        email === "" ||
        phone === "" ||
        enrollment === "" ||
        branch === "" ||
        year === "" ||
        college === "" ||
        cgpa === ""
    ) {

        alert("Please fill all the details.");

        return;
    }


    // =================================================
    // REGEX VALIDATION
    // =================================================

    if (!nameRegex.test(name)) {

        alert("Invalid name. Use only letters and spaces.");

        return;
    }


    if (!emailRegex.test(email)) {

        alert("Please enter a valid email address.");

        return;
    }


    if (!phoneRegex.test(phone)) {

        alert(
            "Phone number must be in this format: +91 98765 43210"
        );

        return;
    }


    if (!enrollmentRegex.test(enrollment)) {

        alert(
            "Invalid enrollment number. Use only letters and numbers."
        );

        return;
    }


    if (!collegeRegex.test(college)) {

        alert(
            "Invalid college name. Use only letters and spaces."
        );

        return;
    }


    // =================================================
    // CGPA VALIDATION
    // =================================================

    if (cgpa < 0 || cgpa > 10) {

        alert("CGPA must be between 0 and 10.");

        return;
    }


    // =================================================
    // SAVE EDITED DATA IN LOCAL STORAGE
    // =================================================

    localStorage.setItem("profileName", name);

    localStorage.setItem("profileEmail", email);

    localStorage.setItem("profilePhone", phone);

    localStorage.setItem(
        "profileEnrollment",
        enrollment
    );

    localStorage.setItem(
        "profileBranch",
        branch
    );

    localStorage.setItem(
        "profileYear",
        year
    );

    localStorage.setItem(
        "profileCollege",
        college
    );

    localStorage.setItem(
        "profileCGPA",
        cgpa
    );


    // =================================================
    // UPDATE DISPLAY
    // =================================================

    document.getElementById("displayName").textContent =
        name;

    document.getElementById("displayCGPA").textContent =
        cgpa;

    document.getElementById("displayYear").textContent =
        year;


    // =================================================
    // DISABLE EDITING
    // =================================================

    document.getElementById("name").disabled = true;

    document.getElementById("email").disabled = true;

    document.getElementById("phone").disabled = true;

    document.getElementById("enrollment").disabled = true;

    document.getElementById("branch").disabled = true;

    document.getElementById("year").disabled = true;

    document.getElementById("college").disabled = true;

    document.getElementById("cgpa").disabled = true;


    // Disable submit button

    document.getElementById("submitBtn").disabled = true;


    // Change edit button

    document.getElementById("editBtn").textContent =
        "✏️ Edit Profile";


    // Success message

    document.getElementById("message").textContent =
        "✅ Profile updated successfully!";
}


// =====================================================
// LOAD SAVED DATA
// =====================================================

function loadSavedData() {

    if (localStorage.getItem("profileName")) {

        document.getElementById("name").value =
            localStorage.getItem("profileName");

        document.getElementById("email").value =
            localStorage.getItem("profileEmail");

        document.getElementById("phone").value =
            localStorage.getItem("profilePhone");

        document.getElementById("enrollment").value =
            localStorage.getItem("profileEnrollment");

        document.getElementById("branch").value =
            localStorage.getItem("profileBranch");

        document.getElementById("year").value =
            localStorage.getItem("profileYear");

        document.getElementById("college").value =
            localStorage.getItem("profileCollege");

        document.getElementById("cgpa").value =
            localStorage.getItem("profileCGPA");


        // Update displayed information

        document.getElementById("displayName").textContent =
            localStorage.getItem("profileName");

        document.getElementById("displayCGPA").textContent =
            localStorage.getItem("profileCGPA");

        document.getElementById("displayYear").textContent =
            localStorage.getItem("profileYear");


        console.log("Profile loaded from Local Storage.");

    }
}


// =====================================================
// PAGE LOAD
// =====================================================

window.onload = function () {

    // First load JSON
    loadJSONProfile();

    // Then check if user has edited/saved data
    loadSavedData();

};