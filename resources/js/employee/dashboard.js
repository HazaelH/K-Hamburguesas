document.addEventListener('DOMContentLoaded', () => {
    const timerElement = document.getElementById('timer');
    
    // Solo ejecutamos si existe el timer (así no da error en otras páginas)
    if (timerElement) {
        console.log('👨‍🍳 Panel de Cocina activo');

        let timeLeft = 30;

        // 1. Cuenta regresiva visual
        const countdown = setInterval(() => {
            timeLeft--;
            if (timeLeft >= 0) {
                timerElement.innerText = timeLeft;
            } else {
                clearInterval(countdown);
            }
        }, 1000);

        // 2. Recarga forzosa a los 30 segundos
        setTimeout(() => {
            console.log('Buscando nuevas comandas...');
            window.location.reload();
        }, 30000);
    }
});