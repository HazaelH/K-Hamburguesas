document.addEventListener('DOMContentLoaded', () => {
    
    // Función global para abrir el modal (Baja o Eliminación definitiva)
    window.confirmarModal = function(formId, tipo) {
        const modal = document.getElementById('dynamic-modal');
        const panel = document.getElementById('dynamic-modal-panel');
        const confirmBtn = document.getElementById('modal-confirm-btn');
        const iconContainer = document.getElementById('modal-icon-container');
        const icon = document.getElementById('modal-icon');
        const title = document.getElementById('modal-title');
        const text = document.getElementById('modal-text');

        if (tipo === 'baja') {
            iconContainer.className = "mx-auto flex h-16 w-16 items-center justify-center rounded-full mb-4 bg-orange-500/20 text-orange-500 border border-orange-500/50";
            icon.className = "fas fa-user-slash text-2xl animate-pulse";
            title.innerText = window.USERS_LANG.soft_title;
            text.innerText = window.USERS_LANG.soft_desc;
            confirmBtn.className = "flex-1 bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 rounded-xl shadow-lg transition-all shadow-orange-900/50 text-sm";
            confirmBtn.innerText = window.USERS_LANG.btn_soft;
        } else {
            iconContainer.className = "mx-auto flex h-16 w-16 items-center justify-center rounded-full mb-4 bg-red-500/20 text-red-500 border border-red-500/50";
            icon.className = "fas fa-skull text-2xl animate-pulse";
            title.innerText = window.USERS_LANG.hard_title;
            text.innerText = window.USERS_LANG.hard_desc;
            confirmBtn.className = "flex-1 bg-red-600 hover:bg-red-500 text-white font-bold py-3 rounded-xl shadow-lg transition-all shadow-red-900/50 text-sm";
            confirmBtn.innerText = window.USERS_LANG.btn_hard;
        }

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            panel.classList.remove('scale-95');
            panel.classList.add('scale-100');
        }, 10);

        confirmBtn.onclick = function() {
            document.getElementById(formId).submit();
        };
    };

    window.cerrarModal = function() {
        const modal = document.getElementById('dynamic-modal');
        const panel = document.getElementById('dynamic-modal-panel');
        
        modal.classList.add('opacity-0');
        panel.classList.remove('scale-100');
        panel.classList.add('scale-95');
        
        setTimeout(() => modal.classList.add('hidden'), 300);
    };

});