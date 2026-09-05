@extends('public.layout')

@section('title', 'Demande de devis menuisier aluminium Lomé | TRENOU')
@section('meta_description', 'Demandez un devis menuisier aluminium Lomé et Togo pour une baie vitrée, une porte-fenêtre, une rénovation ou un aménagement sur mesure.')

@section('content')
    <section class="mx-auto max-w-6xl px-3 py-8 sm:px-6 sm:py-12 lg:px-8">
        <div class="mx-auto mb-6 max-w-2xl text-center sm:mb-8">
            <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-amber-600">Devis personnalisé</p>
            <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Configurez votre projet</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600 sm:text-base">Répondez à quelques questions. Votre estimation indicative se met à jour en temps réel.</p>
        </div>

        @if (session('success'))
            <div role="status" class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                <p class="font-semibold">Vérifiez les informations saisies :</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <script id="quote-config" type="application/json">@json($quoteConfig ?? [])</script>

        <form id="quote-configurator-form" method="POST" action="{{ route('api.devis.store') }}" class="overflow-hidden rounded-[28px] border border-stone-200 bg-white shadow-[0_24px_70px_rgba(15,23,42,0.08)]">
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

            <div class="grid gap-0 lg:grid-cols-[220px_minmax(0,1fr)_280px]">
                <aside class="border-b border-stone-200 bg-stone-50 p-3 sm:p-4 lg:border-b-0 lg:border-r">
                    <div id="stepper" class="grid grid-cols-5 gap-1 lg:flex lg:flex-col lg:gap-3"></div>
                </aside>

                <div class="min-w-0 p-3 sm:p-5 lg:p-7">
                    <div id="step-panels"></div>

                    <div class="mt-5 grid grid-cols-2 gap-3 border-t border-stone-200 pt-4 sm:mt-7 sm:flex sm:items-center sm:justify-between sm:pt-5">
                        <button type="button" id="previous-step" class="hidden min-h-12 items-center justify-center rounded-full border border-stone-200 px-4 text-sm font-semibold text-slate-600 transition hover:border-stone-300 hover:text-slate-900 sm:w-auto">Retour</button>
                        <div class="col-span-2 flex gap-3 sm:col-span-1 sm:ml-auto">
                            <button type="button" id="next-step" class="inline-flex min-h-12 flex-1 items-center justify-center rounded-full bg-amber-500 px-5 py-3 text-sm font-bold text-slate-900 shadow-[0_8px_20px_rgba(245,158,11,0.2)] transition hover:bg-amber-400 disabled:cursor-not-allowed disabled:opacity-40 sm:flex-none sm:w-auto">
                                Suivant
                            </button>
                            <button type="submit" id="submit-quote" class="hidden inline-flex min-h-12 flex-1 items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-bold text-white shadow-[0_8px_20px_rgba(15,23,42,0.18)] transition hover:bg-slate-700 disabled:cursor-wait disabled:opacity-70 sm:flex-none sm:w-auto">
                                Envoyer ma demande
                            </button>
                        </div>
                    </div>
                    <p id="quote-validation-message" role="alert" class="hidden mt-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></p>
                </div>

                <aside class="border-t border-stone-200 bg-slate-900 p-5 text-white lg:border-t-0 lg:border-l lg:p-6">
                    <div class="lg:sticky lg:top-6">
                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-amber-300">Estimation indicative</p>
                        <div id="estimate-range" class="mt-3 text-2xl font-black tracking-tight text-amber-300 sm:text-3xl">—</div>
                        <p id="estimate-meta" class="mt-3 text-sm leading-6 text-slate-300">Complétez les étapes pour obtenir une fourchette indicative.</p>
                        <div class="mt-6 hidden border-t border-white/10 pt-4 text-xs leading-5 text-slate-400 sm:block">Le montant final est confirmé après étude technique et prise de mesures sur site.</div>
                    </div>
                </aside>
            </div>
        </form>
    </section>
@endsection
