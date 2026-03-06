document.addEventListener('DOMContentLoaded', () => {
    
    // ======================================================
    // 0. INICIALIZACIÓN DE INTL-TEL-INPUT (Banderas)
    // ======================================================
    const phoneInput = document.querySelector("#telefono_input");
    const hiddenCountryCode = document.querySelector("#codigo_pais_final");
    let iti;

    if (phoneInput) {
        iti = window.intlTelInput(phoneInput, {
            initialCountry: "mx", // País por defecto
            preferredCountries: ["mx", "us", "br", "co", "ar", "es"], // Países hasta arriba
            separateDialCode: true, // Muestra el +52 fuera del input principal
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/js/utils.js", // Auto-formato inteligente
        });

        // Asegurarnos de guardar el código correcto al inicio
        if(iti.getSelectedCountryData().dialCode) {
            hiddenCountryCode.value = '+' + iti.getSelectedCountryData().dialCode;
        }

        // Si el usuario cambia de país en el dropdown
        phoneInput.addEventListener("countrychange", function() {
            hiddenCountryCode.value = '+' + iti.getSelectedCountryData().dialCode;
        });
    }

    // ======================================================
    // 1. LÓGICA DE ESTILOS DE MÉTODOS DE PAGO
    // ======================================================
    const paymentRadios = document.querySelectorAll('.payment-radio');
    const stripeContainer = document.getElementById('stripe-container');
    
    function updatePaymentVisuals() {
        const selectedMethod = document.querySelector('.payment-radio:checked');
        const selectedValue = selectedMethod ? selectedMethod.value : 'efectivo'; 

        document.querySelectorAll('.payment-label').forEach(label => {
            const icon = label.querySelector('.payment-icon');
            const checkIcon = label.querySelector('.check-icon');
            const radioInput = label.querySelector('.payment-radio');
            
            if (!icon || !checkIcon || !radioInput) return; 

            label.classList.remove('border-orange-500', 'shadow-[0_0_15px_rgba(234,88,12,0.2)]');
            label.classList.add('border-gray-700');
            checkIcon.classList.replace('opacity-100', 'opacity-0');
            
            if (radioInput.value === 'efectivo') icon.classList.replace('text-green-400', 'text-gray-500');
            if (radioInput.value === 'tarjeta_entrega') icon.classList.replace('text-blue-400', 'text-gray-500');
            if (radioInput.value === 'stripe') icon.classList.replace('text-purple-400', 'text-gray-500');
            
            if (radioInput.checked) {
                label.classList.add('border-orange-500', 'shadow-[0_0_15px_rgba(234,88,12,0.2)]');
                label.classList.remove('border-gray-700');
                checkIcon.classList.replace('opacity-0', 'opacity-100');
                
                if (radioInput.value === 'efectivo') icon.classList.replace('text-gray-500', 'text-green-400');
                if (radioInput.value === 'tarjeta_entrega') icon.classList.replace('text-gray-500', 'text-blue-400');
                if (radioInput.value === 'stripe') icon.classList.replace('text-gray-500', 'text-purple-400');
            }
        });

        if (stripeContainer) {
            if (selectedValue === 'stripe') {
                stripeContainer.classList.remove('hidden');
            } else {
                stripeContainer.classList.add('hidden');
            }
        }
    }

    if (paymentRadios.length > 0) {
        paymentRadios.forEach(radio => {
            radio.addEventListener('change', updatePaymentVisuals);
        });
        updatePaymentVisuals();
    }

    // ======================================================
    // 2. INICIALIZACIÓN DE STRIPE Y PROCESAMIENTO
    // ======================================================
    try {
        const stripeKeyMeta = document.querySelector('meta[name="stripe-key"]');
        const stripeKey = stripeKeyMeta ? stripeKeyMeta.getAttribute('content') : null;
        
        let stripe, cardElement;
        const form = document.getElementById('checkout-form'); 
        const btnConfirmar = document.getElementById('btn-confirmar');

        if (stripeKey && stripeKey.trim() !== '' && typeof Stripe !== 'undefined') {
            stripe = Stripe(stripeKey);
            const elements = stripe.elements();

            cardElement = elements.create('card', {
                style: {
                    base: {
                        iconColor: '#f97316', color: '#ffffff', fontWeight: '500', fontFamily: 'system-ui, -apple-system, sans-serif', fontSize: '16px', fontSmoothing: 'antialiased', '::placeholder': { color: '#9ca3af' },
                    },
                    invalid: { iconColor: '#f87171', color: '#f87171' },
                },
                hidePostalCode: true,
            });

            if(document.getElementById('card-element')) {
                cardElement.mount('#card-element');
            }

            cardElement.on('change', function(event) {
                const displayError = document.getElementById('card-errors');
                if (displayError) displayError.textContent = event.error ? event.error.message : '';
            });
        }

        if (btnConfirmar && form) {
            btnConfirmar.addEventListener('click', async (e) => {
                e.preventDefault(); 
                
                // Leemos las traducciones inyectadas
                const msgFill = form.getAttribute('data-msg-fill') || 'Por favor completa los campos.';
                const msgProcess = form.getAttribute('data-msg-process') || 'Procesando...';
                const msgConsentTitle = form.getAttribute('data-msg-consent-title') || 'Acción Requerida';
                const msgConsentMsg = form.getAttribute('data-msg-consent') || 'Debes aceptar los términos.';

                // VALIDACIÓN LEGAL
                const consentCheckbox = document.getElementById('legal_consent');
                if (consentCheckbox && !consentCheckbox.checked) {
                    consentCheckbox.parentElement.parentElement.classList.add('border-red-500', 'bg-red-500/10');
                    setTimeout(() => {
                        consentCheckbox.parentElement.parentElement.classList.remove('border-red-500', 'bg-red-500/10');
                    }, 2000);
                    window.showToast(msgConsentTitle, msgConsentMsg, 'error');
                    return;
                }

                // Validar colonia
                const coloniaVal = document.getElementById('colonia_final').value;
                if(!coloniaVal) {
                    window.showToast('Error', msgFill, 'error'); 
                    return;
                }

                if(phoneInput) {
                    phoneInput.value = phoneInput.value.replace(/\s+/g, '');
                }

                const selectedMethodEl = document.querySelector('input[name="metodo_pago"]:checked');
                const selectedMethod = selectedMethodEl ? selectedMethodEl.value : 'efectivo';
                
                const originalContent = btnConfirmar.innerHTML;
                btnConfirmar.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> ${msgProcess}`;
                btnConfirmar.disabled = true;

                if (selectedMethod === 'stripe') {
                    const {token, error} = await stripe.createToken(cardElement);
                    if (error) {
                        const errDiv = document.getElementById('card-errors');
                        if(errDiv) errDiv.textContent = error.message;
                        btnConfirmar.innerHTML = originalContent;
                        btnConfirmar.disabled = false;
                        return; 
                    } 
                    const hiddenInput = document.createElement('input');
                    hiddenInput.setAttribute('type', 'hidden');
                    hiddenInput.setAttribute('name', 'stripeToken');
                    hiddenInput.setAttribute('value', token.id);
                    form.appendChild(hiddenInput);
                }

                HTMLFormElement.prototype.submit.call(form);
            });
        }

    } catch (e) {
        console.warn("Stripe error.", e);
    }

    // ======================================================
    // 3. LÓGICA DE CÓDIGO POSTAL Y COLONIAS (API)
    // ======================================================
    const cpInput = document.getElementById('cp_input');
    const inputColonia = document.getElementById('colonia_input');
    const autocompleteList = document.getElementById('colonia_autocomplete_list');
    const selectWrapper = document.getElementById('colonia_select_wrapper');
    const selectColonia = document.getElementById('colonia_select');
    const hiddenColoniaFinal = document.getElementById('colonia_final');
    const estadoInput = document.getElementById('estado_input');
    const municipioInput = document.getElementById('municipio_input');
    const loadingMsg = document.getElementById('cp_loading');

    if(inputColonia && selectColonia && hiddenColoniaFinal) {
        inputColonia.addEventListener('input', () => hiddenColoniaFinal.value = inputColonia.value);
        selectColonia.addEventListener('change', () => hiddenColoniaFinal.value = selectColonia.value);

        if(hiddenColoniaFinal.value) {
            inputColonia.value = hiddenColoniaFinal.value;
        }
    }

    if (cpInput) {
        cpInput.addEventListener('input', async function() {
            const cp = this.value.trim();

            if (cp.length === 5) {
                try {
                    if(loadingMsg) loadingMsg.classList.remove('hidden');
                    const response = await fetch(`/api/zip-codes/${cp}`);
                    if (!response.ok) throw new Error('CP error');
                    const data = await response.json();

                    if(estadoInput) estadoInput.value = data.estado;
                    if(municipioInput) municipioInput.value = data.municipio;

                    if(inputColonia) inputColonia.classList.add('hidden');
                    if(autocompleteList) autocompleteList.classList.add('hidden'); 
                    if(selectWrapper) selectWrapper.classList.remove('hidden');
                    
                    if(selectColonia) {
                        selectColonia.innerHTML = '<option value="" disabled selected>--</option>'; 
                        data.colonias.forEach(nombreColonia => {
                            const option = document.createElement('option');
                            option.value = nombreColonia;
                            option.text = nombreColonia;
                            selectColonia.appendChild(option);
                        });

                        if(hiddenColoniaFinal && hiddenColoniaFinal.value) {
                            selectColonia.value = hiddenColoniaFinal.value;
                        }
                    }
                } catch (error) {
                    if(estadoInput) estadoInput.value = '';
                    if(municipioInput) municipioInput.value = '';
                    if(selectWrapper) selectWrapper.classList.add('hidden');
                    if(inputColonia) {
                        inputColonia.classList.remove('hidden');
                    }
                } finally {
                    if(loadingMsg) loadingMsg.classList.add('hidden');
                }
            } else {
                if(selectWrapper && inputColonia) {
                    selectWrapper.classList.add('hidden');
                    inputColonia.classList.remove('hidden');
                }
            }
        });
    }

    if (inputColonia && autocompleteList) {
        let timeout = null;
        inputColonia.addEventListener('input', function(e) {
            const texto = this.value;
            if (texto.length < 3) {
                autocompleteList.classList.add('hidden');
                autocompleteList.innerHTML = '';
                return;
            }
            clearTimeout(timeout);
            timeout = setTimeout(async () => {
                try {
                    const response = await fetch(`/api/colonias/${texto}`);
                    const data = await response.json();
                    autocompleteList.innerHTML = ''; 
                    
                    if(data.length === 0) {
                        autocompleteList.classList.add('hidden');
                        return;
                    }

                    data.forEach(item => {
                        const li = document.createElement('li');
                        li.className = "px-4 py-3 hover:bg-gray-700 cursor-pointer transition-colors";
                        li.innerHTML = `
                            <div class="font-bold text-white">${item.colonia}</div>
                            <div class="text-xs text-gray-400 mt-0.5">${item.municipio}, Estado de México. CP: ${item.codigo_postal}</div>
                        `;
                        li.addEventListener('click', () => {
                            inputColonia.value = item.colonia;
                            if(hiddenColoniaFinal) hiddenColoniaFinal.value = item.colonia;
                            if(cpInput) cpInput.value = item.codigo_postal;
                            if(estadoInput) estadoInput.value = item.estado;
                            if(municipioInput) municipioInput.value = item.municipio;
                            autocompleteList.classList.add('hidden');
                        });
                        autocompleteList.appendChild(li);
                    });
                    autocompleteList.classList.remove('hidden');
                } catch (error) {
                    console.error("Error buscando:", error);
                }
            }, 300);
        });

        document.addEventListener('click', function(e) {
            if (!inputColonia.contains(e.target) && !autocompleteList.contains(e.target)) {
                autocompleteList.classList.add('hidden');
            }
        });
    }

    // =========================================================
    // 4. SISTEMA DE TOASTS UNIFICADO
    // =========================================================
    window.showToast = function(title, msg, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return; 

        const toast = document.createElement('div');
        
        let borderColor = 'border-green-500';
        let iconClass = 'fa-check-circle text-green-500';
        
        if (type === 'error') {
            borderColor = 'border-red-500';
            iconClass = 'fa-exclamation-triangle text-red-500'; 
        } else if (type === 'info') {
            borderColor = 'border-orange-500';
            iconClass = 'fa-info-circle text-orange-500';
        }

        toast.className = `bg-gray-900 text-white p-4 rounded-lg shadow-2xl border-l-4 ${borderColor} transform transition-all duration-300 translate-y-10 opacity-0 min-w-[250px] flex items-start gap-3 pointer-events-auto z-50`;
        toast.innerHTML = `
            <div class="mt-0.5 text-lg"><i class="fas ${iconClass}"></i></div>
            <div>
                <div class="font-bold text-sm leading-tight">${title}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">${msg}</div>
            </div>
        `;

        container.appendChild(toast);
        requestAnimationFrame(() => toast.classList.remove('translate-y-10', 'opacity-0'));

        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, 4000); 
    };

    // =========================================================
    // 5. LÓGICA DE DIRECCIONES GUARDADAS (Autofill Inteligente)
    // =========================================================
    window.abrirModalDirecciones = function() {
        const modal = document.getElementById('modal-direcciones');
        const panel = document.getElementById('modal-direcciones-panel');
        if(!modal || !panel) return;

        modal.classList.remove('hidden');
        setTimeout(() => {
            panel.classList.remove('scale-95', 'opacity-0');
        }, 10);
    };

    window.cerrarModalDirecciones = function() {
        const modal = document.getElementById('modal-direcciones');
        const panel = document.getElementById('modal-direcciones-panel');
        if(!modal || !panel) return;

        panel.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    };

    window.seleccionarDireccion = function(codigo_pais, telefono, calle, numero, cp, colonia, municipio, estado, referencias) {
        
        // 1. Llenar Teléfono y actualizar Bandera Dinámicamente
        if (iti) {
            iti.setNumber(codigo_pais + telefono); 
            hiddenCountryCode.value = '+' + iti.getSelectedCountryData().dialCode;
        }

        // 2. Llenar el resto de campos
        document.getElementById('calle_input').value = calle;
        document.getElementById('numero_input').value = numero;
        
        const cpInput = document.getElementById('cp_input');
        cpInput.value = cp;
        
        document.getElementById('colonia_final').value = colonia;
        
        if(document.getElementById('referencias_input')) {
            document.getElementById('referencias_input').value = referencias;
        }

        // Ejecutar búsqueda CP
        const event = new Event('input', { bubbles: true });
        cpInput.dispatchEvent(event);

        cerrarModalDirecciones();
        
        // Usamos los Data-Attributes inyectados en el formulario
        const form = document.getElementById('checkout-form');
        const title = form.getAttribute('data-msg-addr-loaded') || 'Dirección Cargada';
        const msg = form.getAttribute('data-msg-addr-filled') || 'Tus datos se rellenaron automáticamente.';
        
        window.showToast(title, msg, 'success');
    };
});