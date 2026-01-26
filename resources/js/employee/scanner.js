// resources/js/employee/scanner.js

document.addEventListener('DOMContentLoaded', () => {
    
    // Función que se ejecuta cuando el escáner detecta un código
    function onScanSuccess(decodedText, decodedResult) {
        // 1. Detener escaneo para evitar envíos dobles o loops
        if(html5QrcodeScanner) {
            html5QrcodeScanner.clear();
        }
        
        // 2. Reproducir sonido (Feedback auditivo)
        // Usamos un sonido corto genérico
        try {
            let audio = new Audio('https://actions.google.com/sounds/v1/cartoon/pop.ogg');
            audio.play().catch(e => console.log("Audio bloqueado por el navegador"));
        } catch (e) {
            console.log("Error de audio");
        }

        // 3. Poner el código en el input oculto y enviar formulario
        const input = document.getElementById('codigo-input');
        const form = document.getElementById('scan-form');

        if(input && form) {
            input.value = decodedText;
            form.submit();
        } else {
            console.error("No se encontró el formulario de escaneo.");
        }
    }

    function onScanFailure(error) {
        // No hacer nada si falla un frame (es normal mientras busca el QR)
        // console.warn(`Escaneando... no se detecta código.`);
    }

    // Inicializar el escáner solo si existe el contenedor 'reader'
    if (document.getElementById('reader')) {
        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { 
                fps: 10, // Frames por segundo (10 es balanceado)
                qrbox: {width: 250, height: 250}, // Tamaño de la caja de enfoque
                aspectRatio: 1.0
            },
            /* verbose= */ false
        );
        
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    }
});