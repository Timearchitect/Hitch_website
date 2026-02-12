// Function to load the navbar
function loadNavbar() {
    fetch('navbar.html')
        .then(response => response.text())
        .then(data => {
            const placeholder = document.getElementById('navbar-placeholder');
            placeholder.innerHTML = data;

            // Apply frosted class if the placeholder has it
            const navbar = placeholder.querySelector('.navbar');
            if (placeholder.classList.contains('frosted') && navbar) {
                navbar.classList.add('frosted');
            }

            // Initialize hamburger functionality after navbar is loaded
            initializeHamburger();
        })
        .catch(error => {
            console.error('Error loading navbar:', error);
        });
}

// Function to initialize hamburger functionality
function initializeHamburger() {
    const hamburger = document.querySelector(".hamburger");
    const navMenu = document.querySelector(".nav-menu");

    if (hamburger && navMenu) {
        // Remove any existing listeners to avoid duplicates
        hamburger.removeEventListener("click", toggleHamburger);

        // Add hamburger click event
        hamburger.addEventListener("click", toggleHamburger);

        // Add nav link click events to close menu
        document.querySelectorAll(".nav-link, .nav-menu a").forEach(link => {
            link.addEventListener("click", closeHamburger);
        });
    }
}

// Toggle hamburger menu
function toggleHamburger() {
    const hamburger = document.querySelector(".hamburger");
    const navMenu = document.querySelector(".nav-menu");

    if (hamburger && navMenu) {
        hamburger.classList.toggle("active");
        navMenu.classList.toggle("active");
    }
}

// Close hamburger menu
function closeHamburger() {
    const hamburger = document.querySelector(".hamburger");
    const navMenu = document.querySelector(".nav-menu");

    if (hamburger && navMenu) {
        hamburger.classList.remove("active");
        navMenu.classList.remove("active");
    }
}

// Load navbar when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
    loadNavbar();
});