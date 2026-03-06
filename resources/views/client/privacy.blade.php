@extends('layouts.app')

@section('titulo', __('client/legal.privacy_title'))

@section('contenido')
<div class="py-12 bg-slate-900 min-h-screen">
    <div class="max-w-4xl mx-auto px-6">
        <div class="bg-slate-800 rounded-3xl shadow-2xl border border-slate-700 p-8 sm:p-12 animate-fade-in-up">
            
            <div class="border-b border-slate-700 pb-6 mb-8">
                <h1 class="text-3xl md:text-4xl font-black text-white mb-2">{{ __('client/legal.privacy_title') }}</h1>
                <p class="text-slate-400">{{ __('client/legal.last_updated') }} {{ date('d/m/Y') }}</p>
            </div>

            <div class="space-y-8 text-slate-300 leading-relaxed">
                
                <section>
                    <p>{!! __('client/legal.privacy_intro') !!}</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-white mb-3 border-l-4 border-orange-500 pl-3">{!! __('client/legal.privacy_sec1_title') !!}</h2>
                    <p>{!! __('client/legal.privacy_sec1_p') !!}</p>
                    <ul class="list-disc pl-5 mt-2 space-y-1">
                        <li>{!! __('client/legal.privacy_sec1_li1') !!}</li>
                        <li>{!! __('client/legal.privacy_sec1_li2') !!}</li>
                        <li>{!! __('client/legal.privacy_sec1_li3') !!}</li>
                        <li>{!! __('client/legal.privacy_sec1_li4') !!}</li>
                        <li>{!! __('client/legal.privacy_sec1_li5') !!}</li>
                    </ul>
                    <p class="mt-2 text-sm text-orange-400 bg-orange-500/10 p-3 rounded-lg border border-orange-500/20">
                        {!! __('client/legal.privacy_sec1_note') !!}
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-white mb-3 border-l-4 border-orange-500 pl-3">{!! __('client/legal.privacy_sec2_title') !!}</h2>
                    <p>{!! __('client/legal.privacy_sec2_p1') !!}</p>
                    <ul class="list-disc pl-5 mt-2 space-y-1 mb-4">
                        <li>{!! __('client/legal.privacy_sec2_li1') !!}</li>
                        <li>{!! __('client/legal.privacy_sec2_li2') !!}</li>
                        <li>{!! __('client/legal.privacy_sec2_li3') !!}</li>
                        <li>{!! __('client/legal.privacy_sec2_li4') !!}</li>
                    </ul>
                    <p>{!! __('client/legal.privacy_sec2_p2') !!}</p>
                    <ul class="list-disc pl-5 mt-2 space-y-1">
                        <li>{!! __('client/legal.privacy_sec2_li5') !!}</li>
                        <li>{!! __('client/legal.privacy_sec2_li6') !!}</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-white mb-3 border-l-4 border-orange-500 pl-3">{!! __('client/legal.privacy_sec3_title') !!}</h2>
                    <p>{!! __('client/legal.privacy_sec3_p') !!}</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-white mb-3 border-l-4 border-orange-500 pl-3">{!! __('client/legal.privacy_sec4_title') !!}</h2>
                    <p>{!! __('client/legal.privacy_sec4_p') !!}</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-white mb-3 border-l-4 border-orange-500 pl-3">{!! __('client/legal.privacy_sec5_title') !!}</h2>
                    <p>{!! __('client/legal.privacy_sec5_p1') !!}</p>
                    <p class="mt-2">{!! __('client/legal.privacy_sec5_p2') !!}</p>
                </section>

            </div>
        </div>
    </div>
</div>
@endsection