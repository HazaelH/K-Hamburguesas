@extends('layouts.app')

@section('titulo', __('auth/passwords.forgot_title'))

@section('contenido')
<div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl">
        
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-orange-500/10 rounded-full flex items-center justify-center mx-auto mb-4 text-orange-500 text-3xl">
                <i class="fas fa-key"></i>
            </div>
            <h2 class="text-2xl font-black text-white">{{ __('auth/passwords.forgot_question') }}</h2>
            <p class="text-slate-400 text-sm mt-2">{{ __('auth/passwords.forgot_instruction') }}</p>
        </div>

        @if (session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500 text-emerald-400 px-4 py-3 rounded-xl mb-6 flex gap-3 items-center text-sm">
                <i class="fas fa-check-circle text-lg"></i>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-sm font-bold text-slate-300 mb-2">{{ __('auth/passwords.email') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-slate-500"></i>
                    </div>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="{{ __('auth/passwords.email_placeholder') }}"
                        class="w-full pl-11 pr-4 py-3 bg-slate-950 border @error('email') border-red-500 @else border-slate-700 @enderror rounded-xl text-white focus:outline-none focus:border-orange-500 transition-colors">
                </div>
                @error('email')
                    <p class="text-red-500 text-xs mt-2 font-bold flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-3.5 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                <i class="fas fa-paper-plane"></i> {{ __('auth/passwords.btn_send_link') }}
            </button>

            <div class="text-center pt-4 border-t border-slate-800">
                <a href="{{ route('login') }}" class="text-slate-400 hover:text-white text-sm transition font-bold">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('auth/passwords.back_to_login') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection