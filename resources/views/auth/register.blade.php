@extends('layouts.app')

@section('titulo', __('auth/register.title'))

@section('contenido')
<div class="min-h-[80vh] flex items-center justify-center py-10">
    
    <div class="w-full max-w-5xl bg-slate-900 rounded-3xl shadow-2xl shadow-black/50 overflow-hidden grid grid-cols-1 md:grid-cols-2 border border-slate-300/50">
        
        <div class="hidden md:block relative group order-2 md:order-1">
            <img src="https://images.unsplash.com/photo-1550547660-d9450f859349?q=80&w=1000&auto=format&fit=crop" 
                 class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                 alt="Register Background">
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
            <div class="absolute bottom-0 left-0 p-12 text-white">
                <div class="bg-blue-600 w-12 h-12 rounded-lg flex items-center justify-center mb-4 shadow-lg shadow-blue-600/50">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <h2 class="text-4xl font-bold mb-2 tracking-tight">{!! __('auth/register.club_title') !!}</h2>
                <p class="text-slate-300 text-sm opacity-90">{{ __('auth/register.club_desc') }}</p>
                <div class="flex items-center gap-3 mt-6 pt-6 border-t border-white/20">
                    <div class="flex -space-x-3">
                        <img class="w-8 h-8 rounded-full border-2 border-black" src="https://i.pravatar.cc/100?img=1" alt="">
                        <img class="w-8 h-8 rounded-full border-2 border-black" src="https://i.pravatar.cc/100?img=2" alt="">
                        <img class="w-8 h-8 rounded-full border-2 border-black" src="https://i.pravatar.cc/100?img=3" alt="">
                    </div>
                    <span class="text-xs font-bold text-slate-300">{{ __('auth/register.happy_foodies') }}</span>
                </div>
            </div>
        </div>

        <div class="p-8 md:p-12 flex flex-col justify-center bg-slate-900 relative order-1 md:order-2">
            
            <div class="absolute top-0 right-0 w-40 h-40 bg-blue-500/10 rounded-bl-full pointer-events-none blur-3xl"></div>

            <div class="mb-6 relative z-10">
                <h1 class="text-3xl font-bold text-white mb-2">{{ __('auth/register.form_title') }}</h1>
                <p class="text-slate-400">{{ __('auth/register.form_desc') }}</p>
            </div>

            <form id="register-form" method="POST" action="{{ route('register') }}" class="space-y-4 relative z-10">
                @csrf

                <div>
                    <label class="text-sm font-bold text-slate-300 ml-1">{{ __('auth/register.fullname') }}</label>
                    <div class="relative group mt-1">
                        <span class="absolute left-4 top-3.5 text-slate-500 group-focus-within:text-orange-500 transition-colors">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="{{ __('auth/register.fullname_placeholder') }}"
                               class="w-full bg-slate-950/50 rounded-xl py-3.5 pl-11 pr-4 text-white placeholder-slate-600 focus:outline-none transition-all
                                      @error('name') border border-red-500 focus:ring-red-500 @else border border-slate-300 focus:border-orange-500 focus:ring-orange-500 focus:ring-1 @enderror">
                    </div>
                    @error('name') <p class="text-red-400 text-xs mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-bold text-slate-300 ml-1">{{ __('auth/register.email') }}</label>
                    <div class="relative group mt-1">
                        <span class="absolute left-4 top-3.5 text-slate-500 group-focus-within:text-orange-500 transition-colors">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="{{ __('auth/register.email_placeholder') }}"
                               class="w-full bg-slate-950/50 rounded-xl py-3.5 pl-11 pr-4 text-white placeholder-slate-600 focus:outline-none transition-all
                                      @error('email') border border-red-500 focus:ring-red-500 @else border border-slate-300 focus:border-orange-500 focus:ring-orange-500 focus:ring-1 @enderror">
                    </div>
                    @error('email') <p class="text-red-400 text-xs mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="flex justify-between items-end mb-1">
                            <label class="text-sm font-bold text-slate-300 ml-1">{{ __('auth/register.password') }}</label>
                        </div>
                        <div class="relative group">
                            <span class="absolute left-4 top-3.5 text-slate-500 group-focus-within:text-orange-500 transition-colors">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" name="password" id="reg-password" required placeholder="{{ __('auth/register.password_placeholder') }}"
                                   class="w-full bg-slate-950/50 rounded-xl py-3.5 pl-11 pr-10 text-white placeholder-slate-600 focus:outline-none transition-all
                                          @error('password') border border-red-500 focus:ring-red-500 @else border border-slate-300 focus:border-orange-500 focus:ring-orange-500 focus:ring-1 @enderror">
                            <button type="button" onclick="togglePassword('reg-password', 'eye-icon-reg')" class="absolute right-3 top-3.5 text-slate-500 hover:text-orange-500 transition-colors focus:outline-none">
                                <i id="eye-icon-reg" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label class="text-sm font-bold text-slate-300 ml-1 block mb-1">{{ __('auth/register.confirm_password') }}</label>
                        <div class="relative group">
                            <span class="absolute left-4 top-3.5 text-slate-500 group-focus-within:text-orange-500 transition-colors">
                                <i class="fas fa-check-double"></i>
                            </span>
                            <input type="password" name="password_confirmation" id="reg-password-confirm" required placeholder="{{ __('auth/register.password_placeholder') }}"
                                   class="w-full bg-slate-950/50 border border-slate-300 rounded-xl py-3.5 pl-11 pr-10 text-white placeholder-slate-600 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 focus:outline-none transition-all">
                            <button type="button" onclick="togglePassword('reg-password-confirm', 'eye-icon-reg-conf')" class="absolute right-3 top-3.5 text-slate-500 hover:text-orange-500 transition-colors focus:outline-none">
                                <i id="eye-icon-reg-conf" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <p class="text-[10px] text-slate-500 ml-1">
                    {{ __('auth/register.password_help') }}
                </p>

                @error('password') 
                    <div class="bg-red-500/10 border border-red-500/50 rounded-lg p-2 mt-2">
                        <p class="text-red-400 text-xs font-bold flex items-center gap-1">
                            <i class="fas fa-times-circle"></i> {{ $message }}
                        </p>
                    </div>
                @enderror

                <button type="submit" id="register-btn"
                        class="w-full bg-orange-700 hover:bg-orange-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-orange-700/40 transform transition hover:-translate-y-0.5 active:scale-95 flex items-center justify-center gap-2 mt-6">
                    <span id="register-btn-text">{{ __('auth/register.btn_register') }}</span>
                    <i id="register-btn-icon" class="fas fa-user-plus hidden"></i>
                </button>
            </form>

            {{-- DIVISOR Y BOTÓN DE GOOGLE --}}
            <div class="relative my-6 z-10">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-700"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-3 bg-slate-900 text-slate-400 font-bold uppercase tracking-wider text-xs">{{ __('auth/register.or') }}</span>
                </div>
            </div>

            <a href="{{ route('google.register') }}" class="w-full flex items-center justify-center gap-3 bg-white text-slate-800 font-black py-3.5 rounded-xl border border-slate-300 shadow-md hover:bg-slate-100 transition-all relative z-10 active:scale-95 transform hover:-translate-y-0.5">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-5 h-5">
                {{ __('auth/register.google_btn') }}
            </a>

            {{-- CORRECCIÓN: TEXTO DE INICIAR SESIÓN NORMAL --}}
            <div class="mt-6 text-center border-t border-slate-800 pt-6 relative z-10">
                <p class="text-slate-400 text-sm">
                    {{ __('auth/register.already_member') }} 
                    <a href="{{ route('login') }}" class="text-orange-500 font-bold hover:text-orange-400 hover:underline transition">
                        {{ __('auth/register.login_here') }}
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
        const form = document.getElementById('register-form');
        const btn = document.getElementById('register-btn');
        const btnIcon = document.getElementById('register-btn-icon');
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
                    
                    btnIcon.classList.remove('hidden', 'fa-user-plus');
                    btnIcon.classList.add('fa-spinner', 'fa-spin');
                }
            });
        }
    });
</script>
@endsection