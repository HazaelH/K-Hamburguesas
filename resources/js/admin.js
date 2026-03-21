document.addEventListener('DOMContentLoaded', () => {
    
    // Elementos del DOM
    const toggleBtn = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    // Función para alternar el menú
    function toggleSidebar() {
        if (!sidebar || !overlay) return;

        // En Tailwind, -translate-x-full oculta el elemento a la izquierda.
        // Al quitarlo, el elemento vuelve a su posición original (visible).
        sidebar.classList.toggle('-translate-x-full');
        
        // Mostramos/Ocultamos el fondo oscuro
        overlay.classList.toggle('hidden');
    }

    // Listeners
    if(toggleBtn) {
        toggleBtn.addEventListener('click', toggleSidebar);
    }

    if(overlay) {
        // Cerrar al hacer clic fuera del menú
        overlay.addEventListener('click', toggleSidebar);
    }
});