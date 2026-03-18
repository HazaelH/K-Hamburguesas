// Declaramos la variable globalmente, pero NO la iniciamos todavía (ahorramos memoria)
let nucleoLector = null;

document.addEventListener('DOMContentLoaded', () => {
    
    const cuadroSeleccion = document.getElementById('dialogo-seleccion-lente');
    const panelIntero = document.getElementById('panel-lente');
    const cuadroCarga = document.getElementById('dialogo-procesando');
    const cubiertaInicial = document.getElementById('pantalla-inicio-lector');
    const contornoLector = document.getElementById('borde-decorativo');

    // 1. Asignamos las funciones del botón PRIMERO (Así nunca fallarán)
    window.abrirOpcionesLente = () => {
        if(cuadroSeleccion && panelIntero) {
            cuadroSeleccion.classList.remove('hidden');
            setTimeout(() => panelIntero.classList.remove('translate-y-full'), 10);
        }
    };

    window.cerrarOpcionesLente = () => {
        if(cuadroSeleccion && panelIntero) {
            panelIntero.classList.add('translate-y-full');
            setTimeout(() => cuadroSeleccion.classList.add('hidden'), 300);
        }
    };

    // 2. Lógica de la cámara (Lazy Loading)
    window.iniciarLectura = (tipoOptica) => {
        window.cerrarOpcionesLente();
        
        // LA MAGIA: Inicializamos el lector SOLO si el usuario ya eligió una cámara
        if (!nucleoLector) {
            try {
                nucleoLector = new Html5Qrcode('reader');
            } catch (error) {
                console.error("Error al cargar la librería del escáner:", error);
                alert(window.DELIVERY_LANG.cam_access_error || "Error al cargar la cámara.");
                return;
            }
        }
        
        const parametros = { 
            fps: 10, 
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        };

        nucleoLector.start(
            { facingMode: tipoOptica }, 
            parametros, 
            procesarCaptura, 
            () => {} 
        ).then(() => {
            if(cubiertaInicial) cubiertaInicial.classList.add('opacity-0');
            setTimeout(() => { if(cubiertaInicial) cubiertaInicial.classList.add('hidden'); }, 300);
            if(contornoLector) contornoLector.classList.remove('hidden');
        }).catch(err => {
            alert(window.DELIVERY_LANG.cam_access_error || "No se pudo acceder a la óptica del dispositivo.");
        });
    };

    function procesarCaptura(textoLocalizado) {
        if(nucleoLector) {
            nucleoLector.stop().then(() => {
                if(cuadroCarga) cuadroCarga.classList.remove('hidden');
                
                try {
                    let sonido = new Audio('https://actions.google.com/sounds/v1/cartoon/pop.ogg');
                    sonido.play().catch(() => {});
                } catch (e) {}

                const campoDato = document.getElementById('codigo-input');
                const peticionFormulario = document.getElementById('scan-form');

                if(campoDato && peticionFormulario) {
                    campoDato.value = textoLocalizado;
                    setTimeout(() => {
                        peticionFormulario.submit();
                    }, 800);
                }
            }).catch(err => console.log("Detención de hardware interrumpida", err));
        }
    }
});