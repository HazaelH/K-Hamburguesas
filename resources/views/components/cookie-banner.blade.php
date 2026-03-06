<div id="cookie-banner" class="fixed bottom-0 inset-x-0 z-[999] hidden pb-4 sm:pb-6 px-4 sm:px-6 pointer-events-none">
    <div class="max-w-4xl mx-auto bg-slate-900/95 backdrop-blur-md border border-slate-700 shadow-[0_-10px_40px_rgba(0,0,0,0.5)] p-5 sm:p-6 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-5 pointer-events-auto transform translate-y-full transition-transform duration-700 ease-out" id="cookie-panel">
        
        <div class="flex-1 text-center md:text-left flex flex-col sm:flex-row items-center gap-4">
            <div class="w-12 h-12 bg-orange-500/10 rounded-full flex items-center justify-center text-orange-500 shrink-0">
                <i class="fas fa-cookie-bite text-2xl"></i>
            </div>
            <p class="text-slate-300 text-sm leading-relaxed">
                {{ __('client/cookies.message') }}
                <a href="{{ route('privacy') }}" class="text-orange-500 hover:text-orange-400 font-bold underline transition-colors whitespace-nowrap">
                {{ __('client/cookies.privacy_link') }}
                </a>
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto shrink-0">
            <button onclick="acceptCookies()" class="w-full sm:w-auto bg-orange-600 hover:bg-orange-500 text-white font-bold py-2.5 px-6 rounded-xl transition-all shadow-[0_0_15px_rgba(234,88,12,0.3)] hover:shadow-[0_0_25px_rgba(234,88,12,0.5)]">
                {{ __('client/cookies.accept') }}
            </button>
            <button onclick="rejectCookies()" class="w-full sm:w-auto bg-slate-800 hover:bg-slate-700 border border-slate-600 text-slate-300 font-bold py-2.5 px-6 rounded-xl transition-all">
                {{ __('client/cookies.reject') }}
            </button>
        </div>

    </div>
</div>

<script>
    // --- LÓGICA 100% REALISTA DE MANEJO DE COOKIES ---

    // 1. Función que inyecta Google Analytics SOLO si hay permiso
    function loadMarketingScripts() {
        const consent = localStorage.getItem('k_cookies_consent');
        
        if (consent === 'accepted') {
            // Si aceptó, creamos la etiqueta <script> de Google Analytics y la inyectamos
            const script = document.createElement('script');
            script.src = 'https://www.googletagmanager.com/gtag/js?id=G-REALISTA123'; // ID de prueba
            script.async = true;
            document.head.appendChild(script);

            script.onload = function() {
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', 'G-REALISTA123');
                console.log('Google Analytics activado: El usuario aceptó las cookies.');
            };
        } else {
            console.log('Analytics bloqueado: El usuario rechazó las cookies o no ha decidido.');
        }
    }

    // 2. Control del Banner Visual
    document.addEventListener('DOMContentLoaded', function() {
        const banner = document.getElementById('cookie-banner');
        const panel = document.getElementById('cookie-panel');
        
        const cookieConsent = localStorage.getItem('k_cookies_consent');
        
        if (!cookieConsent) {
            // Si no ha decidido, mostramos el banner
            banner.classList.remove('hidden');
            setTimeout(() => {
                panel.classList.remove('translate-y-full');
            }, 500);
        } else {
            // Si ya había decidido antes, revisamos si podemos cargar los scripts
            loadMarketingScripts();
        }
    });

    function acceptCookies() {
        localStorage.setItem('k_cookies_consent', 'accepted');
        closeCookieBanner();
        // Cargamos los scripts INMEDIATAMENTE sin recargar la página
        loadMarketingScripts(); 
    }

    function rejectCookies() {
        localStorage.setItem('k_cookies_consent', 'rejected');
        closeCookieBanner();
        // No cargamos nada, mantenemos la privacidad del usuario a salvo
    }

    function closeCookieBanner() {
        const panel = document.getElementById('cookie-panel');
        panel.classList.add('translate-y-full');
        setTimeout(() => {
            document.getElementById('cookie-banner').classList.add('hidden');
        }, 700);
    }
</script>