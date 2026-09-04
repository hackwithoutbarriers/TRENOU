@extends('public.layout')

@section('title', 'Partager mon avis TRENOU')
@section('meta_description', 'Partagez votre avis, votre photo de projet et votre consentement pour devenir un témoignage vérifié TRENOU.')

@section('content')
    <section class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="rounded-[32px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="mb-8">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Témoignage client</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Partager mon avis</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">Merci pour votre confiance. Votre avis sera publié après validation, et nous n’affichons que les informations avec votre consentement explicite.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('reviews.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="nom_client" class="mb-2 block text-sm font-medium text-slate-700">Nom du client</label>
                        <input id="nom_client" name="nom_client" type="text" value="{{ old('nom_client', $prefillName) }}" required class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                    </div>
                    <div>
                        <label for="ville" class="mb-2 block text-sm font-medium text-slate-700">Ville</label>
                        <input id="ville" name="ville" type="text" value="{{ old('ville') }}" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-3">
                    <div>
                        <label for="projet_type" class="mb-2 block text-sm font-medium text-slate-700">Type de projet</label>
                        <input id="projet_type" name="projet_type" type="text" value="{{ old('projet_type') }}" placeholder="mobilier, baie vitrée..." class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                    </div>
                    <div>
                        <label for="projet_ref" class="mb-2 block text-sm font-medium text-slate-700">Référence du devis</label>
                        <input id="projet_ref" name="projet_ref" type="text" value="{{ old('projet_ref', $prefillProjectRef) }}" placeholder="DEV-00001" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                    </div>
                    <div>
                        <label for="date_projet" class="mb-2 block text-sm font-medium text-slate-700">Date du projet</label>
                        <input id="date_projet" name="date_projet" type="date" value="{{ old('date_projet', now()->toDateString()) }}" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="note" class="mb-2 block text-sm font-medium text-slate-700">Note</label>
                        <select id="note" name="note" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                            @for ($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" {{ old('note', 5) == $i ? 'selected' : '' }}>{{ $i }} étoile{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="photo_projet" class="mb-2 block text-sm font-medium text-slate-700">Photo du projet</label>
                        <input id="photo_projet" name="photo_projet" type="file" accept="image/*" capture="environment" required class="block w-full rounded-2xl border border-stone-300 bg-white px-3 py-3 text-sm text-slate-700 file:mr-4 file:rounded-full file:border-0 file:bg-amber-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-amber-700 hover:file:bg-amber-200">
                        <span class="mt-1 block text-xs text-slate-500">Choisissez une photo dans votre galerie ou prenez-la avec l’appareil photo.</span>
                    </div>
                </div>

                <div>
                    <label for="texte" class="mb-2 block text-sm font-medium text-slate-700">Votre avis</label>
                    <textarea id="texte" name="texte" rows="6" required class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-200" placeholder="Décrivez votre expérience, la qualité du travail, le professionnalisme et la finition...">{{ old('texte') }}</textarea>
                </div>

                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                    <label class="flex items-start gap-3 text-sm text-slate-700">
                        <input type="checkbox" name="consentement" value="1" class="mt-1 h-4 w-4 rounded border-stone-300 text-amber-600 focus:ring-amber-500" {{ old('consentement') ? 'checked' : '' }} required>
                        <span>
                            J’accepte que mon nom, ma ville et ma photo soient publiés sur le site TRENOU, à condition qu’il s’agisse bien d’un projet réel et que ce témoignage soit vérifié.
                        </span>
                    </label>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <a href="{{ route('reviews') }}" class="text-sm font-medium text-slate-600 transition hover:text-slate-800">Retour aux avis</a>
                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700">Envoyer mon témoignage</button>
                </div>
            </form>
        </div>
    </section>
@endsection
