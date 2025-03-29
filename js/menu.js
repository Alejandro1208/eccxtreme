document.addEventListener("DOMContentLoaded", function() {
    const menuToggle = document.getElementById("menuToggle");
    const mainNav = document.getElementById("mainNav");
    const body = document.body;

    if (menuToggle && mainNav) {
        // Toggle del menú
        menuToggle.addEventListener("click", function(e) {
            e.stopPropagation(); // Prevenimos la propagación del evento
            this.classList.toggle("active");
            mainNav.classList.toggle("active");
            body.style.overflow = mainNav.classList.contains("active") ? "hidden" : "auto";
        });

        // Cerrar menú al hacer clic en enlaces
        const navLinks = mainNav.querySelectorAll("a");
        navLinks.forEach(link => {
            link.addEventListener("click", () => {
                menuToggle.classList.remove("active");
                mainNav.classList.remove("active");
                body.style.overflow = "auto";
            });
        });

        // Cerrar menú al hacer clic fuera
        document.addEventListener("click", (e) => {
            if (mainNav.classList.contains('active') && 
                !mainNav.contains(e.target) && 
                !menuToggle.contains(e.target)) {
                menuToggle.classList.remove("active");
                mainNav.classList.remove("active");
                body.style.overflow = "auto";
            }
        });

        // Prevenir que el menú se cierre al hacer clic dentro
        mainNav.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }
});