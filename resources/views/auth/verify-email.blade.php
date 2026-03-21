@extends('layouts.app')

@section('titulo', __('auth/verify.title'))

@section('contenido')
<div class="min-h-[60vh] flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full bg-slate-900 rounded-2xl shadow-2xl p-8 border border-slate-700 text-center">
        
        <div class="bg-orange-600/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-envelope-open-text text-3xl text-orange-500"></i>
        </div>

        <h2 class="text-2xl font-bold text-white mb-2">{{ __('auth/verify.heading') }}</h2>
        <p class="text-slate-400 mb-6">
            {{ __('auth/verify.instruction') }}
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 bg-green-500/10 border border-green-500/50 text-green-400 text-sm p-3 rounded-lg">
                {{ __('auth/verify.link_sent') }}
            </div>
        @endif

        <div class="space-y-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-orange-900/20">
                    {{ __('auth/verify.btn_resend') }}
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-slate-500 hover:text-white text-sm font-semibold underline decoration-slate-700 hover:decoration-white transition">
                    {{ __('auth/verify.btn_logout') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection