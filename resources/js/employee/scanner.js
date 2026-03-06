document.addEventListener('DOMContentLoaded', () => {
    const contenedorID = 'reader';
    const areaLector = document.getElementById(contenedorID);
    
    if (!areaLector) return;

    let nucleoLector = new Html5Qrcode(contenedorID);
    
    const cuadroSeleccion = document.getElementById('dialogo-seleccion-lente');
    const panelIntero = document.getElementById('panel-lente');
    const cuadroCarga = document.getElementById('dialogo-procesando');
    const cubiertaInicial = document.getElementById('pantalla-inicio-lector');
    const contornoLector = document.getElementById('borde-decorativo');

    window.abrirOpcionesLente = () => {
        cuadroSeleccion.classList.remove('hidden');
        setTimeout(() => panelIntero.classList.remove('translate-y-full'), 10);
    };

    window.cerrarOpcionesLente = () => {
        panelIntero.classList.add('translate-y-full');
        setTimeout(() => cuadroSeleccion.classList.add('hidden'), 300);
    };

    window.iniciarLectura = (tipoOptica) => {
        window.cerrarOpcionesLente();
        
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
            cubiertaInicial.classList.add('opacity-0');
            setTimeout(() => cubiertaInicial.classList.add('hidden'), 300);
            contornoLector.classList.remove('hidden');
        }).catch(err => {
            alert(window.DELIVERY_LANG.cam_access_error);
        });
    };

    function procesarCaptura(textoLocalizado) {
        
        nucleoLector.stop().then(() => {
            
            cuadroCarga.classList.remove('hidden');
            
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
});