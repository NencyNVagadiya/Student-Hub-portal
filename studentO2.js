// =========================================
// SIDEBAR
// =========================================

function toggleMenu() {

    const sidebar = document.getElementById("sidebar");

    if (sidebar.style.left === "0px") {
        sidebar.style.left = "-300px";
    } else {
        sidebar.style.left = "0px";
    }
}


// =========================================
// DARK MODE
// =========================================

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

        } else {

            localStorage.setItem("darkMode", "disabled");

            toggle.textContent = "🌙";
        }

    });
}


// =========================================
// GET LEAVE SUGGESTION
// =========================================

function getSuggestion() {

    const reason =
        document.getElementById("reason").value;

    const departure =
        document.getElementById("departure").value;

    const returnDate =
        document.getElementById("returnDate").value;

    const days =
        Number(document.getElementById("days").value);

    const attendance =
        Number(document.getElementById("attendance").value);

    const exam =
        document.getElementById("exam").value;


    const suggestionBox =
        document.getElementById("suggestionBox");

    const suggestionTitle =
        document.getElementById("suggestionTitle");

    const suggestionText =
        document.getElementById("suggestionText");


    // Check dates

    if (departure === "" || returnDate === "") {

        alert("Please enter departure and return date.");

        return;
    }


    // Check number of days

    if (isNaN(days) || days < 1 || days > 30) {

        alert("⚠️ Number of days must be between 1 and 30.");

        return;
    }


    // Show suggestion box

    suggestionBox.style.display = "block";


    // Exam condition

    if (exam === "Yes") {

        suggestionTitle.textContent =
            "⚠️ Avoid Leave!";

        suggestionText.textContent =
            "You have an important exam during your leave. It is better to avoid leave and attend the exam.";
    }


    // Low attendance

    else if (attendance < 75) {

        suggestionTitle.textContent =
            "⚠️ Leave Not Recommended";

        suggestionText.textContent =
            "Your attendance is below 75%. Taking " +
            days +
            " days of leave may affect your attendance. Try to avoid unnecessary leave.";
    }


    // Good attendance

    else {

        suggestionTitle.textContent =
            "✔ Leave May Be Manageable";

        suggestionText.textContent =
            "Your attendance is " +
            attendance +
            "%. You may consider taking " +
            days +
            " days of leave for " +
            reason +
            ". Make sure to cover missed lectures and assignments.";
    }


    // =========================================
    // UPDATE SUMMARY
    // =========================================

    document.getElementById("summaryReason").textContent =
        reason;

    document.getElementById("summaryFrom").textContent =
        departure;

    document.getElementById("summaryTo").textContent =
        returnDate;

    document.getElementById("summaryDays").textContent =
        days + " Days";

    document.getElementById("summaryAttendance").textContent =
        attendance + "%";

    document.getElementById("summaryExam").textContent =
        exam;
}


// =========================================
// NUMBER OF DAYS VALIDATION
// =========================================

const daysInput =
    document.getElementById("days");

if (daysInput) {

    daysInput.addEventListener("input", function () {

        let value = Number(this.value);


        // Maximum 30 days

        if (value > 30) {

            this.value = 30;

            alert("⚠️ Maximum 30 days allowed.");
        }


        // Minimum 1 day

        if (value < 1 && this.value !== "") {

            this.value = 1;

            alert("⚠️ Minimum 1 day required.");
        }

    });
}


// =========================================
// GENERATE PDF APPLICATION
// =========================================

function generateApplication() {

    const reason =
        document.getElementById("reason").value;

    const departure =
        document.getElementById("departure").value;

    const returnDate =
        document.getElementById("returnDate").value;

    const days =
        document.getElementById("days").value;

    const attendance =
        document.getElementById("attendance").value;

    const exam =
        document.getElementById("exam").value;


    // Check dates

    if (departure === "" || returnDate === "") {

        alert(
            "Please enter Departure Date and Return Date first."
        );

        return;
    }


    // Check jsPDF

    if (!window.jspdf) {

        alert("PDF library is not loaded.");

        return;
    }


    // Create PDF

    const { jsPDF } = window.jspdf;

    const pdf = new jsPDF();


    // =========================================
    // PDF HEADING
    // =========================================

    pdf.setFontSize(20);

    pdf.text(
        "STUDENTHUB",
        105,
        20,
        {
            align: "center"
        }
    );


    pdf.setFontSize(16);

    pdf.text(
        "Leave Application",
        105,
        32,
        {
            align: "center"
        }
    );


    pdf.line(
        20,
        38,
        190,
        38
    );


    // =========================================
    // LEAVE DETAILS
    // =========================================

    pdf.setFontSize(12);


    pdf.text(
        "Reason for Leave:",
        20,
        55
    );

    pdf.text(
        reason,
        75,
        55
    );


    pdf.text(
        "Departure Date:",
        20,
        68
    );

    pdf.text(
        departure,
        75,
        68
    );


    pdf.text(
        "Return Date:",
        20,
        81
    );

    pdf.text(
        returnDate,
        75,
        81
    );


    pdf.text(
        "Number of Days:",
        20,
        94
    );

    pdf.text(
        days + " Days",
        75,
        94
    );


    pdf.text(
        "Current Attendance:",
        20,
        107
    );

    pdf.text(
        attendance + "%",
        75,
        107
    );


    pdf.text(
        "Exam During Leave:",
        20,
        120
    );

    pdf.text(
        exam,
        75,
        120
    );


    // =========================================
    // LEAVE REQUEST
    // =========================================

    pdf.text(
        "Leave Request:",
        20,
        145
    );


    const message =
        "I request leave due to " +
        reason +
        ". I will be away from " +
        departure +
        " to " +
        returnDate +
        " for " +
        days +
        " days. I will make sure to complete the missed academic work.";


    const lines =
        pdf.splitTextToSize(
            message,
            170
        );


    pdf.text(
        lines,
        20,
        158
    );


    // =========================================
    // SIGNATURES
    // =========================================

    pdf.text(
        "Student Signature: ____________________",
        20,
        200
    );


    pdf.text(
        "Faculty Signature: ____________________",
        180,
        200,
        {
            align: "right"
        }
    );


    // =========================================
    // SAVE PDF
    // =========================================

    pdf.save(
        "StudentHub_Leave_Application.pdf"
    );
}