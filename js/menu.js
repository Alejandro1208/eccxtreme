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

    // Optimización automática de imágenes
    function optimizeImages() {
        // 1. Aplicar lazy loading a todas las imágenes que no sean del header/logo
        const images = document.querySelectorAll('img:not(.logo img, .main-header img)');
        
        images.forEach(img => {
            // Si ya tiene loading="lazy", no hacer nada
            if (img.getAttribute('loading') !== 'lazy') {
                img.setAttribute('loading', 'lazy');
                
                // Asegurar dimensiones para evitar layout shift
                if (!img.hasAttribute('width') && !img.hasAttribute('height')) {
                    const width = img.naturalWidth || img.offsetWidth;
                    const height = img.naturalHeight || img.offsetHeight;
                    if (width && height) {
                        img.setAttribute('width', width);
                        img.setAttribute('height', height);
                    }
                }
            }
        });

        const criticalImages = [
            '/images/logo.png',
            '/images/main-banner.webp'
        ];
        
        criticalImages.forEach(src => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.href = src;
            link.as = 'image';
            document.head.appendChild(link);
        });
    }

    // Ejecutar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        // ... código existente del menú ...
        
        // Añadir optimización de imágenes
        optimizeImages();
    });
});