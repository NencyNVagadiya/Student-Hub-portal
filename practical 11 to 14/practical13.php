<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 13: Fetch API JSON CRUD (No Page Reload)</title>
    <link rel="stylesheet" href="practical13.css">
</head>
<body>

<div class="container">

    <!-- Top Navigation Bar between Practicals -->
    <nav class="top-nav">
        <div>
            <strong>WDF Practical 13:</strong> Fetch API & JSON CRUD
        </div>
        <div>
            <a href="index.php" style="margin-right: 14px;">&larr; Practical 12 (Events)</a>
            <a href="practical14_login.php">Practical 14 (Admin Dashboard) &rarr;</a>
        </div>
    </nav>

    <!-- Page Header -->
    <header class="header-box">
        <h1>Student Management System</h1>
        <p>Practical 13: Asynchronous CRUD using PHP JSON API & JavaScript Fetch API (No Page Reload)</p>
    </header>

    <!-- Dynamic Feedback Alerts (Success / Error) -->
    <div id="alertContainer" class="alert-container"></div>

    <!-- Main Grid Layout -->
    <div class="grid-layout">

        <!-- Form Card (Add / Edit Student) -->
        <div class="card">
            <h2 id="formTitle" class="card-title">Add New Student</h2>
            <p class="card-subtitle">Submit the form to create or update student records asynchronously.</p>

            <form id="studentForm">
                <!-- Hidden ID for Edit Operations -->
                <input type="hidden" id="student_id" name="id" value="">

                <div class="form-group">
                    <label for="name">Full Name <span class="required">*</span></label>
                    <input
                        type="text"
                        id="name"
                        class="form-control"
                        placeholder="e.g., Aarav Mehta"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input
                        type="email"
                        id="email"
                        class="form-control"
                        placeholder="e.g., aarav@example.com"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="course">Course / Branch <span class="required">*</span></label>
                    <select id="course" class="form-control" required>
                        <option value="">-- Select Course --</option>
                        <option value="Computer Engineering">Computer Engineering</option>
                        <option value="Information Technology">Information Technology</option>
                        <option value="AI & Data Science">AI & Data Science</option>
                        <option value="Electronics & Communication">Electronics & Communication</option>
                        <option value="Mechanical Engineering">Mechanical Engineering</option>
                        <option value="Civil Engineering">Civil Engineering</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="semester">Semester (1–8) <span class="required">*</span></label>
                    <input
                        type="number"
                        id="semester"
                        class="form-control"
                        min="1"
                        max="8"
                        placeholder="e.g., 4"
                        required
                    >
                </div>

                <div class="form-actions">
                    <button type="submit" id="submitBtn" class="btn btn-primary">Add Student</button>
                    <button type="button" id="cancelBtn" class="btn btn-secondary hidden">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Table Card (Student List) -->
        <div class="card">
            <div class="table-header">
                <div>
                    <h2 class="card-title">Registered Students</h2>
                    <p class="card-subtitle" style="margin-bottom: 0;">Live list loaded via <code>GET api.php</code></p>
                </div>
                <button type="button" id="refreshBtn" class="btn btn-refresh" title="Reload data from API">&#8635; Refresh</button>
            </div>

            <!-- Loading Spinner / Indicator -->
            <div id="loadingState" class="state-box">
                Loading students from server...
            </div>

            <!-- Empty Data State -->
            <div id="emptyState" class="state-box hidden">
                No students found. Use the form on the left to add a new student.
            </div>

            <!-- Students Table -->
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th style="width: 80px;">Sem</th>
                            <th style="width: 130px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="studentsTableBody">
                        <!-- Populated dynamically by practical13.js -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<!-- Vanilla JS Fetch API Controller -->
<script src="practical13.js"></script>

</body>
</html>
