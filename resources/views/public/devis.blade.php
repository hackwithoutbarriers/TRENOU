@extends('public.layout')

@section('title', 'Demande de devis menuisier aluminium Lomé | TRENOU')
@section('meta_description', 'Demandez un devis menuisier aluminium Lomé et Togo pour une baie vitrée, une porte-fenêtre, une rénovation ou un aménagement sur mesure.')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="mb-6 text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Devis</p>
            <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Configurez votre projet</h1>
            <p class="mt-3 text-sm text-slate-600 sm:text-base">Répondez en 5 étapes pour simuler votre devis en temps réel.</p>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <script id="quote-config" type="application/json">@json($quoteConfig ?? [])</script>

        <form id="quote-configurator-form" method="POST" action="{{ route('api.devis.store') }}" class="rounded-[28px] border border-stone-200 bg-white p-5 shadow-[0_20px_50px_rgba(15,23,42,0.05)] sm:p-7">
            @csrf

            <input type="hidden" name="categorie" value="{{ old('categorie') }}">
            <input type="hidden" name="sous_type" value="{{ old('sous_type') }}">
            <input type="hidden" name="dimensions" value='{{ old('dimensions') ?: json_encode(['largeur' => '', 'hauteur' => '', 'longueur' => '']) }}'>
            <input type="hidden" name="finition" value="{{ old('finition', 'ral') }}">
            <input type="hidden" name="vitrage" value="{{ old('vitrage', 'double') }}">
            <input type="hidden" name="options" value='{{ old('options') ?: '[]' }}'>
            <input type="hidden" name="estimation" value='{{ old('estimation') ?: json_encode(['min' => 0, 'max' => 0, 'devise' => 'FCFA']) }}'>
            <input type="hidden" name="source" value="simulateur">
            <textarea name="description_besoin" class="hidden">{{ old('description_besoin') }}</textarea>

            <div class="grid gap-6 lg:grid-cols-[240px_minmax(0,1fr)_300px]">
                <aside class="rounded-3xl border border-stone-200 bg-stone-50 p-2 sm:p-3">
                    <div id="stepper" class="grid grid-cols-5 gap-1 lg:flex lg:flex-col lg:gap-3"></div>
                </aside>

                <div class="min-w-0">
                    <div id="step-panels" class="space-y-5"></div>

                    <div class="mt-6 flex items-center justify-between gap-3 border-t border-stone-200 pt-5">
                        <button type="button" id="previous-step" class="hidden text-sm font-medium text-slate-500 transition hover:text-slate-900">Retour</button>
                        <div class="ml-auto flex items-center gap-3">
                            <button type="button" id="next-step" class="inline-flex items-center justify-center rounded-full bg-amber-500 px-5 py-3 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-amber-400 disabled:cursor-not-allowed disabled:opacity-40">
                                Suivant
                            </button>
                            <button type="submit" id="submit-quote" class="hidden inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700">
                                Envoyer ma demande
                            </button>
                        </div>
                    </div>
                </div>

                <aside class="rounded-3xl border border-stone-200 bg-slate-900 p-5 text-white shadow-sm">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-amber-300">Estimation indicative</p>
                    <div id="estimate-range" class="mt-3 text-2xl font-black tracking-tight text-amber-300">—</div>
                    <p id="estimate-meta" class="mt-3 text-sm text-slate-300">Complétez les étapes pour obtenir une fourchette indicative.</p>
                </aside>
            </div>
        </form>
    </section>
@endsection
