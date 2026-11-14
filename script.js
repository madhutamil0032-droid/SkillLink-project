// SkillLink homepage interactions
document.addEventListener("DOMContentLoaded", function () {
    const year = document.getElementById("year");
    if (year) year.textContent = new Date().getFullYear();

    const searchForm = document.getElementById("serviceSearch");
    const serviceInput = document.getElementById("serviceInput");
    const areaInput = document.getElementById("areaInput");
    const searchMessage = document.getElementById("searchMessage");

    if (searchForm) {
        searchForm.addEventListener("submit", function (event) {
            const service = serviceInput.value.trim();
            const area = areaInput.value.trim();

            if (!service || !area) {
                event.preventDefault();
                if (searchMessage) {
                    searchMessage.textContent = "Please enter a service and your area.";
                }
                return;
            }

            serviceInput.value = service;
            areaInput.value = area;
            // The browser submits the GET form to backend/search_providers.php.
        });
    }

    document.querySelectorAll(".service-card").forEach(function (card) {
        card.addEventListener("click", function () {
            const selectedService = card.getAttribute("data-service");
            if (serviceInput && selectedService) {
                serviceInput.value = selectedService;
                serviceInput.focus();
                const searchFormTop = searchForm.getBoundingClientRect().top + window.scrollY;
                window.scrollTo({ top: searchFormTop - 120, behavior: "smooth" });
            }
        });
    });

    const menuToggle = document.getElementById("menuToggle");
    const navLinks = document.getElementById("navLinks");

    if (menuToggle && navLinks) {
        menuToggle.addEventListener("click", function () {
            const expanded = menuToggle.getAttribute("aria-expanded") === "true";
            menuToggle.setAttribute("aria-expanded", String(!expanded));
            navLinks.classList.toggle("active");
        });

        navLinks.querySelectorAll("a").forEach(function (link) {
            link.addEventListener("click", function () {
                navLinks.classList.remove("active");
                menuToggle.setAttribute("aria-expanded", "false");
            });
        });
    }
});
