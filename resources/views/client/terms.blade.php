@extends('layouts.app')

@section('titulo', __('client/legal.terms_title'))

@section('contenido')
<div class="py-12 bg-slate-900 min-h-screen">
    <div class="max-w-4xl mx-auto px-6">
        <div class="bg-slate-800 rounded-3xl shadow-2xl border border-slate-300 p-8 sm:p-12 animate-fade-in-up">
            
            <div class="border-b border-slate-300 pb-6 mb-8">
                <h1 class="text-3xl md:text-4xl font-black text-white mb-2">{{ __('client/legal.terms_title') }}</h1>
                <p class="text-slate-400">{{ __('client/legal.last_updated') }} {{ date('d/m/Y') }}</p>
            </div>

            <div class="space-y-8 text-slate-300 leading-relaxed">
                
                <section>
                    <h2 class="text-xl font-bold text-white mb-3 flex items-center gap-2"><i class="fas fa-file-contract text-orange-500"></i> {!! __('client/legal.terms_sec1_title') !!}</h2>
                    <p>{!! __('client/legal.terms_sec1_p') !!}</p>
                    {{-- AQUÍ INSERTAMOS LA ACLARACIÓN DE GOOGLE --}}
                    <p class="mt-4 text-sm text-slate-400 bg-slate-900 p-4 rounded-xl border border-slate-700">{!! __('client/legal.terms_sec1_p2') !!}</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-white mb-3 flex items-center gap-2"><i class="fas fa-hamburger text-orange-500"></i> {!! __('client/legal.terms_sec2_title') !!}</h2>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>{!! __('client/legal.terms_sec2_li1') !!}</li>
                        <li>{!! __('client/legal.terms_sec2_li2') !!}</li>
                        <li>{!! __('client/legal.terms_sec2_li3') !!}</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-white mb-3 flex items-center gap-2"><i class="fas fa-motorcycle text-orange-500"></i> {!! __('client/legal.terms_sec3_title') !!}</h2>
                    <p>{!! __('client/legal.terms_sec3_p') !!}</p>
                    <ul class="list-disc pl-5 space-y-2 mt-2">
                        <li>{!! __('client/legal.terms_sec3_li1') !!}</li>
                        <li>{!! __('client/legal.terms_sec3_li2') !!}</li>
                        <li>{!! __('client/legal.terms_sec3_li3') !!}</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-white mb-3 flex items-center gap-2"><i class="fas fa-credit-card text-orange-500"></i> {!! __('client/legal.terms_sec4_title') !!}</h2>
                    <p>{!! __('client/legal.terms_sec4_p1') !!}</p>
                    <p class="mt-2">{!! __('client/legal.terms_sec4_p2') !!}</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-white mb-3 flex items-center gap-2"><i class="fas fa-undo-alt text-orange-500"></i> {!! __('client/legal.terms_sec5_title') !!}</h2>
                    <p>{!! __('client/legal.terms_sec5_p1') !!}</p>
                    <p class="mt-2">{!! __('client/legal.terms_sec5_p2') !!}</p>
                    <ul class="list-disc pl-5 space-y-2 mt-2">
                        <li>{!! __('client/legal.terms_sec5_li1') !!}</li>
                        <li>{!! __('client/legal.terms_sec5_li2') !!}</li>
                    </ul>
                    <p class="mt-2 text-sm text-slate-400">{!! __('client/legal.terms_sec5_p3') !!}</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-white mb-3 flex items-center gap-2"><i class="fas fa-gavel text-orange-500"></i> {!! __('client/legal.terms_sec6_title') !!}</h2>
                    <p>{!! __('client/legal.terms_sec6_p') !!}</p>
                </section>

            </div>
        </div>
    </div>
</div>
@endsection