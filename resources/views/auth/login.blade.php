@extends('layouts.app')

@section('titulo', __('auth/login.title'))

@section('contenido')

@vite(['resources/css/auth.css'])

<div class="min-h-[85vh] flex items-center justify-center py-10 px-4">
    
    <div class="w-full max-w-5xl bg-slate-900 rounded-3xl shadow-2xl shadow-black/60 overflow-hidden grid grid-cols-1 md:grid-cols-2 border border-slate-300/50">
        
        <div class="hidden md:block relative group overflow-hidden bg-black">
            <div class="absolute inset-0 w-full h-full">
                <div class="slideshow-bg" style="background-image: url('https://images.unsplash.com/photo-1568901346375-23c9450c58cd?q=80&w=1000&auto=format&fit=crop'); animation-delay: 0s;"></div>
                <div class="slideshow-bg" style="background-image: url('https://images.unsplash.com/photo-1550547660-d9450f859349?q=80&w=1000&auto=format&fit=crop'); animation-delay: 6s;"></div>
                <div class="slideshow-bg" style="background-image: url('https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?q=80&w=1000&auto=format&fit=crop'); animation-delay: 12s;"></div>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent z-10"></div>
            <div class="absolute bottom-0 left-0 p-12 text-white z-20">
                <div class="bg-orange-700 w-12 h-12 rounded-lg flex items-center justify-center mb-4 shadow-lg shadow-orange-700/50 backdrop-blur-sm">
                    <i class="fas fa-crown text-2xl"></i>
                </div>
                <p class="text-4xl font-bold mb-2 tracking-tight">{!! __('auth/login.slogan') !!}</p>
                <p class="text-slate-300 text-sm opacity-90 drop-shadow-md">{{ __('auth/login.slogan_desc') }}</p>
            </div>
        </div>

        <div class="p-8 md:p-12 flex flex-col justify-center bg-slate-900 relative">
            
            <div class="absolute top-0 right-0 w-32 h-32 bg-orange-500/10 rounded-bl-full pointer-events-none blur-3xl"></div>

            <div class="mb-8 relative z-10">
                <h1 class="text-3xl font-bold text-white mb-2">{{ __('auth/login.welcome_back') }}</h1>
                <p class="text-slate-400">{{ __('auth/login.instruction') }}</p>
            </div>

            @if (session('status'))
                <div class="mb-4 bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded-xl text-sm relative z-10">
                    {{ session('status') }}
                </div>
            @endif

            <form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-5 relative z-10">
                @csrf

                <div class="space-y-2">
                    <label for="email" class="text-sm font-bold text-slate-300 ml-1">{{ __('auth/login.email') }}</label>
                    <div class="relative group">
                        <span class="input-icon absolute left-4 top-3.5 text-slate-500 transition-colors duration-300">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="{{ __('auth/login.email_placeholder') }}"
                               class="w-full bg-slate-950/50 rounded-xl py-3.5 pl-11 pr-4 text-white placeholder-slate-600 focus:outline-none transition-all shadow-inner
                                      @error('email') border border-red-500 focus:ring-1 focus:ring-red-500 @else border border-slate-300 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 @enderror">
                    </div>
                    @error('email') 
                        <p class="text-red-400 text-xs mt-1 ml-1 font-bold animate-pulse">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p> 
                    @enderror
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center mb-1">
                        <label for="password" class="text-sm font-bold text-slate-300 ml-1">{{ __('auth/login.password') }}</label>
                        <a href="{{ route('password.request') }}" class="text-xs text-orange-500 hover:text-orange-400 transition font-bold">
                            {{ __('auth/login.forgot_password') }}
                        </a>
                    </div>
                    
                    <div class="relative group">
                        <span class="input-icon absolute left-4 top-3.5 text-slate-500 transition-colors duration-300">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" required placeholder="{{ __('auth/login.password_placeholder') }}"
                            class="w-full bg-slate-950/50 border border-slate-300 rounded-xl py-3.5 pl-11 pr-12 text-white placeholder-slate-600 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 focus:outline-none transition-all shadow-inner">
                        
                        <button type="button" aria-label="Mostrar u ocultar contraseña" onclick="togglePassword('password', 'eye-icon-login')" class="absolute right-4 top-3.5 text-slate-500 hover:text-orange-500 transition-colors focus:outline-none">
                            <i id="eye-icon-login" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" id="login-btn"
                        class="w-full bg-orange-700 hover:bg-orange-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-orange-700/40 transform transition hover:-translate-y-0.5 active:scale-95 flex items-center justify-center gap-2 mt-4">
                    <span id="login-btn-text">{{ __('auth/login.btn_login') }}</span>
                    <i id="login-btn-icon" class="fas fa-arrow-right text-sm"></i>
                </button>
            </form>
            
            {{-- DIVISOR Y BOTÓN DE GOOGLE --}}
            <div class="relative my-6 z-10">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-700"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-3 bg-slate-900 text-slate-400 font-bold uppercase tracking-wider text-xs">{{ __('auth/login.or') }}</span>
                </div>
            </div>

            <a href="{{ route('google.login') }}" class="w-full flex items-center justify-center gap-3 bg-white text-slate-800 font-black py-3.5 rounded-xl border border-slate-300 shadow-md hover:bg-slate-100 transition-all relative z-10 active:scale-95 transform hover:-translate-y-0.5">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-5 h-5">
                {{ __('auth/login.google_btn') }}
            </a>

            <div class="mt-8 text-center border-t border-slate-800 pt-6 relative z-10">
                <p class="text-slate-400 text-sm">
                    {{ __('auth/login.no_account') }} 
                    <a href="{{ route('register') }}" class="text-orange-500 font-bold hover:text-orange-400 hover:underline transition">
                        {{ __('auth/login.register_free') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('login-form');
        const btn = document.getElementById('login-btn');
        const btnIcon = document.getElementById('login-btn-icon');
        let isSubmitting = false;

        if(form && btn) {
            form.addEventListener('submit', function(e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return;
                }
                
                if(form.checkValidity()) {
                    isSubmitting = true;
                    btn.disabled = true;
                    btn.style.pointerEvents = 'none';
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                    btn.classList.remove('hover:-translate-y-0.5', 'hover:bg-orange-500', 'active:scale-95');
                    
                    btnIcon.className = 'fas fa-spinner fa-spin text-sm';
                }
            });
        }
    });
</script>
@endsection