let notices = [];

let currentPage = 1;

let itemsPerPage = 3;


// ========================================
// FETCH JSON DATA
// ========================================

fetch("notices.json")

    .then(response => response.json())

    .then(data => {

        notices = data;

        displayNotices();

    })

    .catch(error => {

        console.log("Error loading JSON:", error);

    });


// ========================================
// DISPLAY NOTICES
// ========================================

function displayNotices() {

    let searchText =
        document.getElementById("search").value.toLowerCase();

    let category =
        document.getElementById("filter").value;

    let sortType =
        document.getElementById("sort").value;


    // SEARCH
    let filteredNotices = notices.filter(notice => {

        let matchesSearch =
            notice.title.toLowerCase().includes(searchText) ||
            notice.description.toLowerCase().includes(searchText);

        let matchesCategory =
            category === "All" ||
            notice.category === category;

        return matchesSearch && matchesCategory;

    });


    // SORTING

    if (sortType === "newest") {

        filteredNotices.sort(
            (a, b) => new Date(b.date) - new Date(a.date)
        );

    }

    else if (sortType === "oldest") {

        filteredNotices.sort(
            (a, b) => new Date(a.date) - new Date(b.date)
        );

    }

    else if (sortType === "az") {

        filteredNotices.sort(
            (a, b) => a.title.localeCompare(b.title)
        );

    }

    else if (sortType === "za") {

        filteredNotices.sort(
            (a, b) => b.title.localeCompare(a.title)
        );

    }


    // ========================================
    // PAGINATION
    // ========================================

    let start =
        (currentPage - 1) * itemsPerPage;

    let end =
        start + itemsPerPage;

    let pageNotices =
        filteredNotices.slice(start, end);


    // ========================================
    // DISPLAY
    // ========================================

    let container =
        document.getElementById("noticeContainer");

    container.innerHTML = "";


    if (pageNotices.length === 0) {

        container.innerHTML =
            "<p>No notices found.</p>";

        document.getElementById("pagination").innerHTML = "";

        return;
    }


    pageNotices.forEach(notice => {

        let card = document.createElement("div");

        card.className = "notice-card";

        card.innerHTML = `

            <h2>${notice.title}</h2>

            <p>
                <b>Category:</b>
                ${notice.category}
            </p>

            <p>
                <b>Date:</b>
                ${notice.date}
            </p>

            <p>
                ${notice.description}
            </p>

        `;

        container.appendChild(card);

    });


    // CREATE PAGINATION

    createPagination(filteredNotices.length);

}


// ========================================
// PAGINATION BUTTONS
// ========================================

function createPagination(totalItems) {

    let totalPages =
        Math.ceil(totalItems / itemsPerPage);

    let pagination =
        document.getElementById("pagination");

    pagination.innerHTML = "";


    for (let i = 1; i <= totalPages; i++) {

        let button =
            document.createElement("button");

        button.textContent = i;

        button.onclick = function () {

            currentPage = i;

            displayNotices();

        };

        pagination.appendChild(button);

    }

}

//-----------------------------
//dark mode toggle
//-----------------------------

const toggle = document.getElementById("theme-toggle");

/* Apply saved theme when page opens */
if (localStorage.getItem("darkMode") === "enabled") {
    document.body.classList.add("dark-mode");

    if (toggle) {
        toggle.textContent = "☀️";
    }
}

/* Change theme */
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

//-----------------------------
//sidebar toggle
//-----------------------------

function toggleMenu(){

    const sidebar = document.getElementById("sidebar");/* It selects an HTML element by its ID.*/

    if(sidebar.style.left=="0px"){
        sidebar.style.left="-300px";
    }
    else{
        sidebar.style.left="0px";
    }

}
