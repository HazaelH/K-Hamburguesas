@extends('layouts.app')

@section('titulo', __('auth/passwords.reset_title'))

@section('contenido')
<div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl">
        
        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-white">{{ __('auth/passwords.reset_heading') }}</h2>
            <p class="text-slate-400 text-sm mt-2">{{ __('auth/passwords.reset_instruction') }}</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-sm font-bold text-slate-300 mb-2">{{ __('auth/passwords.email') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required readonly
                    class="w-full px-4 py-3 bg-slate-950/50 border border-slate-800 rounded-xl text-slate-500 cursor-not-allowed">
                @error('email')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-bold text-slate-300 mb-2">{{ __('auth/passwords.new_password') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-slate-500"></i>
                    </div>
                    <input id="reset-password" type="password" name="password" required placeholder="{{ __('auth/passwords.new_password_placeholder') }}"
                        class="w-full pl-11 pr-12 py-3 bg-slate-950 border border-slate-700 rounded-xl text-white focus:outline-none focus:border-orange-500 transition-colors">
                    
                    <button type="button" onclick="togglePassword('reset-password', 'eye-icon-reset')" class="absolute right-4 top-3.5 text-slate-500 hover:text-orange-500 transition-colors focus:outline-none">
                        <i id="eye-icon-reset" class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-bold text-slate-300 mb-2">{{ __('auth/passwords.confirm_password') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-slate-500"></i>
                    </div>
                    <input id="reset-password-confirm" type="password" name="password_confirmation" required placeholder="{{ __('auth/passwords.confirm_placeholder') }}"
                        class="w-full pl-11 pr-12 py-3 bg-slate-950 border border-slate-700 rounded-xl text-white focus:outline-none focus:border-orange-500 transition-colors">
                    
                    <button type="button" onclick="togglePassword('reset-password-confirm', 'eye-icon-reset-conf')" class="absolute right-4 top-3.5 text-slate-500 hover:text-orange-500 transition-colors focus:outline-none">
                        <i id="eye-icon-reset-conf" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3.5 rounded-xl shadow-lg transition-all mt-4 flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> {{ __('auth/passwords.btn_save_password') }}
            </button>
        </form>
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
</script>
@endsection