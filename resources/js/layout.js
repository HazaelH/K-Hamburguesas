// resources/js/layout.js

document.addEventListener('DOMContentLoaded', () => {
    
    // Lógica del Navbar Responsivo
    const btn = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');
    const icon = document.getElementById('menu-icon');

    if(btn && menu && icon) {
        btn.addEventListener('click', () => {
            if (menu.style.maxHeight) {
                // Cerrar
                menu.style.maxHeight = null;
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            } else {
                // Abrir
                menu.style.maxHeight = menu.scrollHeight + "px";
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            }
        });
    }
});