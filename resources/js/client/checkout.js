document.addEventListener('DOMContentLoaded', () => {
    // Referencias al DOM
    const cpInput = document.getElementById('cp_input');
    const coloniaInput = document.getElementById('colonia_input');
    const datalist = document.getElementById('lista_colonias');
    const estadoInput = document.getElementById('estado_input');
    const municipioInput = document.getElementById('municipio_input');
    const loadingMsg = document.getElementById('cp_loading');

    // Variable temporal para guardar lo que devuelve la búsqueda por nombre
    let resultadosBusquedaNombre = []; 

    // ======================================================
    // CASO 1: BÚSQUEDA POR CÓDIGO POSTAL (Exacta)
    // ======================================================
    if (cpInput) {
        cpInput.addEventListener('input', async function() {
            const cp = this.value;

            // Solo actuamos si son exactamente 5 dígitos
            if (cp.length === 5) {
                try {
                    // Feedback visual
                    loadingMsg.classList.remove('hidden');
                    
                    const response = await fetch(`/api/zip-codes/${cp}`);
                    
                    if (!response.ok) throw new Error('CP no encontrado');
                    
                    // Tu controlador devuelve: { estado, municipio, colonias: [...] }
                    const data = await response.json();

                    // 1. Llenar Estado y Municipio
                    if(estadoInput) estadoInput.value = data.estado;
                    if(municipioInput) municipioInput.value = data.municipio;

                    // 2. Llenar el Datalist con las colonias (Array simple de strings)
                    datalist.innerHTML = ''; // Limpiar anteriores
                    coloniaInput.value = ''; // Limpiar campo para que elija
                    coloniaInput.placeholder = "Selecciona tu colonia...";
                    
                    data.colonias.forEach(nombreColonia => {
                        const option = document.createElement('option');
                        option.value = nombreColonia; 
                        datalist.appendChild(option);
                    });

                    // Si solo hay una colonia, la seleccionamos automáticamente
                    if (data.colonias.length === 1) {
                        coloniaInput.value = data.colonias[0];
                    } else {
                        coloniaInput.focus(); // Mandamos el cursor a colonia
                    }

                } catch (error) {
                    console.error("Error CP:", error);
                    // Si falla, limpiamos para que escriba manual
                    estadoInput.value = '';
                    municipioInput.value = '';
                    coloniaInput.placeholder = "Escribe tu colonia...";
                } finally {
                    loadingMsg.classList.add('hidden');
                }
            }
        });
    }

    // ======================================================
    // CASO 2: BÚSQUEDA POR NOMBRE DE COLONIA
    // ======================================================
    if (coloniaInput) {
        let timeout = null;

        coloniaInput.addEventListener('input', function(e) {
            const texto = this.value;

            // ----------------------------------------------------
            // A. DETECTAR SI EL USUARIO SELECCIONÓ UNA OPCIÓN
            // ----------------------------------------------------
            // Buscamos si lo que escribió coincide con algún resultado previo de la API
            const seleccion = resultadosBusquedaNombre.find(item => item.colonia === texto);
            
            if (seleccion) {
                // ¡Bingo! Eligió una colonia de la lista de búsqueda
                cpInput.value = seleccion.codigo_postal;
                estadoInput.value = seleccion.estado;
                municipioInput.value = seleccion.municipio;
                return; // Terminamos, no seguimos buscando
            }

            // ----------------------------------------------------
            // B. BUSCAR MIENTRAS ESCRIBE
            // ----------------------------------------------------
            
            // Si ya hay un CP válido (5 dígitos), asumimos que está filtrando 
            // la lista del CASO 1, así que no llamamos a la API de búsqueda por nombre.
            if (cpInput.value.length === 5) return;

            // Limpiamos timeout anterior para no saturar
            clearTimeout(timeout);

            // Solo buscamos si escribe al menos 4 letras
            if (texto.length < 4) return;

            timeout = setTimeout(async () => {
                try {
                    const response = await fetch(`/api/colonias/${texto}`);
                    // Tu controlador devuelve array de objetos: [{colonia, codigo_postal, ...}, ...]
                    const data = await response.json();

                    // Guardamos resultados en memoria para el paso A
                    resultadosBusquedaNombre = data;

                    // Llenar el Datalist
                    datalist.innerHTML = '';
                    
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.colonia; 
                        // En el label mostramos ayuda visual: "Colonia - Municipio (CP)"
                        option.label = `${item.municipio}, CP ${item.codigo_postal}`;
                        datalist.appendChild(option);
                    });

                } catch (error) {
                    console.error("Error buscando colonia:", error);
                }
            }, 300); // Espera 300ms
        });
    }
});