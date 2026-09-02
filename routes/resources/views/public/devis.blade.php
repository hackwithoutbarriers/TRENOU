@extends('public.layout')

@section('title', 'Demande de devis menuisier aluminium Lomé | TRENOU')
@section('meta_description', 'Demandez un devis menuisier aluminium Lomé et Togo pour une baie vitrée, une porte-fenêtre, une rénovation ou un aménagement sur mesure.')

@section('content')
    <section class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="mb-6 text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Devis</p>
            <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Décrivez votre besoin</h1>
            <p class="mt-3 text-sm text-slate-600 sm:text-base">Remplissez ce formulaire et recevez une réponse rapide de notre équipe.</p>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('public.devis.store') }}" class="rounded-3xl border border-stone-200 bg-white p-5 shadow-sm sm:p-7">
            @csrf

            <div class="grid gap-5 sm:grid-cols-2">
                <label class="text-sm font-medium text-slate-700">
                    <span class="mb-2 block">Nom complet</span>
                    <input name="nom" value="{{ old('nom') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-3 py-3 text-slate-900 focus:border-amber-400 focus:outline-none" placeholder="Votre nom" />
                    @error('nom') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                </label>

                <label class="text-sm font-medium text-slate-700">
                    <span class="mb-2 block">Téléphone</span>
                    <input name="telephone" value="{{ old('telephone') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-3 py-3 text-slate-900 focus:border-amber-400 focus:outline-none" placeholder="+228 ..." />
                    @error('telephone') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                </label>

                <label class="text-sm font-medium text-slate-700">
                    <span class="mb-2 block">Ville</span>
                    <input name="ville" value="{{ old('ville') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-3 py-3 text-slate-900 focus:border-amber-400 focus:outline-none" placeholder="Lomé" />
                    @error('ville') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                </label>

                <label class="text-sm font-medium text-slate-700">
                    <span class="mb-2 block">Pays</span>
                    <input name="pays" value="{{ old('pays', 'Togo') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-3 py-3 text-slate-900 focus:border-amber-400 focus:outline-none" placeholder="Togo" />
                    @error('pays') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                </label>
            </div>

            <label class="mt-5 block text-sm font-medium text-slate-700">
                <span class="mb-2 block">Description du besoin</span>
                <textarea name="description_besoin" rows="6" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-3 py-3 text-slate-900 focus:border-amber-400 focus:outline-none" placeholder="Décrivez votre projet, votre budget, votre délai et les besoins spécifiques.">{{ old('description_besoin') }}</textarea>
                @error('description_besoin') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
            </label>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-between">
                <p class="text-xs text-slate-500">Votre demande est enregistrée et visible dans le back-office de l'artisan.</p>
                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-amber-500 px-5 py-3 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-amber-400">
                    Envoyer ma demande
                </button>
            </div>
        </form>
    </section>
@endsection
