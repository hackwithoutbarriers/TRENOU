@extends('public.layout')

@section('title', 'Contact | '.config('business.name'))
@section('meta_description', 'Contactez '.config('business.name').' pour vos travaux de '.config('business.activities'))

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-[1fr_0.9fr]">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Contact</p>
                <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Parlons de votre projet</h1>
                <p class="mt-4 text-base leading-7 text-slate-600">
                    {{ config('business.activities') }} Notre atelier vous accompagne à Lomé et dans tout le Togo.
                </p>

                <div class="mt-8 space-y-4 text-sm text-slate-700">
                    <div class="rounded-2xl border border-stone-200 bg-white p-4">
                        <span class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Téléphone</span>
                        <a href="https://wa.me/{{ config('business.phone_digits') }}" class="mt-2 inline-block font-semibold text-emerald-600">{{ config('business.phone') }}</a>
                    </div>
                    <div class="rounded-2xl border border-stone-200 bg-white p-4">
                        <span class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Email</span>
                        <a href="mailto:{{ config('business.email') }}" class="mt-2 inline-block font-semibold text-slate-900">{{ config('business.email') }}</a>
                    </div>
                    <div class="rounded-2xl border border-stone-200 bg-white p-4">
                        <span class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Adresse</span>
                        <p class="mt-2 font-semibold text-slate-900">{{ config('business.address') }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                @if (session('success'))
                    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
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

                <h2 class="text-xl font-bold text-slate-900">Besoin d’un retour rapide ?</h2>
                <p class="mt-3 text-sm leading-7 text-slate-600">Échangez directement avec l’équipe via WhatsApp ou passez par le formulaire ci-dessous.</p>

                <form method="POST" action="{{ route('contact.store') }}" class="mt-6 space-y-4">
                    @csrf

                    <label class="block text-sm font-medium text-slate-700">
                        <span class="mb-2 block">Nom</span>
                        <input name="nom" value="{{ old('nom') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-3 py-3 text-slate-900 focus:border-amber-400 focus:outline-none" placeholder="Votre nom" />
                        @error('nom')<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror
                    </label>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block text-sm font-medium text-slate-700">
                            <span class="mb-2 block">Email</span>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-3 py-3 text-slate-900 focus:border-amber-400 focus:outline-none" placeholder="vous@example.com" />
                            @error('email')<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror
                        </label>

                        <label class="block text-sm font-medium text-slate-700">
                            <span class="mb-2 block">Téléphone</span>
                            <input type="tel" inputmode="tel" name="telephone" value="{{ old('telephone') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-3 py-3 text-slate-900 focus:border-amber-400 focus:outline-none" placeholder="+228 ..." />
                            @error('telephone')<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror
                        </label>
                    </div>

                    <label class="block text-sm font-medium text-slate-700">
                        <span class="mb-2 block">Objet</span>
                        <input name="sujet" value="{{ old('sujet') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-3 py-3 text-slate-900 focus:border-amber-400 focus:outline-none" placeholder="Demande de devis, rénovation, etc." />
                        @error('sujet')<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror
                    </label>

                    <label class="block text-sm font-medium text-slate-700">
                        <span class="mb-2 block">Message</span>
                        <textarea name="message" rows="5" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-3 py-3 text-slate-900 focus:border-amber-400 focus:outline-none" placeholder="Détailler votre besoin ...">{{ old('message') }}</textarea>
                        @error('message')<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror
                    </label>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <a href="{{ 'https://wa.me/' . config('services.whatsapp.number', '22890585976') . '?text=' . urlencode('Bonjour, je souhaite en savoir plus.') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-full bg-emerald-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-600">
                            WhatsApp
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-amber-500 px-5 py-3 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-amber-400">
                            Envoyer le message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
