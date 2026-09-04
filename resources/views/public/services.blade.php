@extends('public.layout')

@section('title', 'Menuiserie aluminium Lomé | TRENOU')
@section('meta_description', 'Menuiserie aluminium Lomé, baie vitrée aluminium Togo et services de construction et mobilier sur mesure pour les particuliers et professionnels.')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Nos domaines</p>
            <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Services de menuiserie aluminium et construction à Lomé.</h1>
            <p class="mt-4 text-base leading-7 text-slate-600">
                Nous réalisons des missions complètes, de la conception à la finition, en tenant compte des standards de qualité, du climat côtier et des besoins spécifiques des clients locaux comme de la diaspora.
            </p>
        </div>

        <div class="mt-8 grid gap-5 lg:grid-cols-2">
            @foreach ($serviceCategories as $category)
                <article id="{{ $category['slug'] }}" class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-xl">
                        {{ $category['slug'] === 'menuiserie-batiment' ? '🏗️' : ($category['slug'] === 'menuiserie-aluminium' ? '🪟' : '🪑') }}
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900">{{ $category['title'] }}</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ $category['summary'] }}</p>

                    <ul class="mt-5 space-y-2 text-sm text-slate-600">
                        @foreach ($category['highlights'] as $item)
                            <li class="flex items-center gap-2"><span class="inline-block h-2 w-2 rounded-full bg-amber-500"></span>{{ $item }}</li>
                        @endforeach
                    </ul>

                    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('services.detail', ['slug' => $category['slug']]) }}" class="inline-flex min-h-11 items-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700">
                            Voir le service
                        </a>
                        <a href="{{ route('public.devis') }}" class="inline-flex min-h-11 items-center rounded-full border border-stone-200 bg-stone-50 px-4 py-2.5 text-sm font-semibold text-slate-800 transition hover:bg-stone-100">
                            Demander un devis
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection
