@extends('public.layout')

@section('title', 'Avis clients TRENOU | Témoignages vérifiés et Google')
@section('meta_description', 'Découvrez les avis clients TRENOU, les témoignages vérifiés et les notes Google pour un artisan fiable au Togo.')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-[32px] border border-stone-200 bg-gradient-to-br from-slate-950 via-slate-900 to-stone-800 p-6 shadow-[0_24px_60px_-30px_rgba(15,23,42,0.8)] sm:p-8 lg:p-10">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-amber-300">Preuve sociale</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight text-white sm:text-4xl lg:text-5xl">Avis clients TRENOU</h1>
                    <p class="mt-4 max-w-xl text-sm leading-6 text-slate-300">Les projets finis en beauté, les notes Google, et les témoignages vérifiés de vrais clients. Tout est pensé pour inspirer confiance avant même la première prise de contact.</p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="rounded-full border border-white/10 bg-white/5 px-4 py-3 backdrop-blur-sm">
                        <div class="flex items-center gap-3">
                            <div class="text-3xl font-black text-white">{{ number_format((float) $summary['average'], 1, ',', ' ') }}</div>
                            <div class="flex items-center gap-1 text-lg text-amber-400" aria-label="Note moyenne {{ $summary['average'] }} sur 5">
                                @for ($star = 1; $star <= 5; $star++)
                                    <span>{{ $star <= round($summary['average']) ? '★' : '☆' }}</span>
                                @endfor
                            </div>
                        </div>
                        <div class="mt-1 text-xs uppercase tracking-[0.2em] text-slate-300">{{ $summary['count'] }} avis</div>
                    </div>

                    <a href="{{ route('reviews.share') }}" class="inline-flex items-center justify-center rounded-full bg-amber-400 px-5 py-3 text-sm font-semibold text-slate-900 shadow-lg shadow-amber-500/20 transition hover:-translate-y-0.5 hover:bg-amber-300">Partager mon avis</a>
                </div>
            </div>

            @if (session('success'))
                <div class="mt-6 rounded-2xl border border-emerald-400/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mt-8 grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
                <div class="rounded-[28px] border border-white/10 bg-white/5 p-5 backdrop-blur-sm">
                    <div class="text-xs uppercase tracking-[0.22em] text-amber-200">Satisfaction globale</div>
                    <div class="mt-3 text-5xl font-black text-white">{{ number_format((float) $summary['average'], 1, ',', ' ') }}</div>
                    <div class="mt-3 flex items-center gap-1 text-xl text-amber-400">
                        @for ($star = 1; $star <= 5; $star++)
                            <span>{{ $star <= round($summary['average']) ? '★' : '☆' }}</span>
                        @endfor
                    </div>
                    <div class="mt-5 flex items-center gap-2 text-xs font-medium uppercase tracking-[0.2em] text-slate-300">
                        <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                        Google + témoignages vérifiés
                    </div>
                    <p class="mt-4 text-sm leading-6 text-slate-300">Nos avis combinent les notes Google et les témoignages internes vérifiés avec photo et consentement explicite.</p>
                </div>

                <div class="space-y-3 rounded-[28px] border border-white/10 bg-slate-900/50 p-5">
                    @foreach ([5, 4, 3, 2, 1] as $rating)
                        @php
                            $count = $summary['distribution'][$rating] ?? 0;
                            $percentage = $summary['count'] > 0 ? ($count / $summary['count']) * 100 : 0;
                        @endphp
                        <div class="grid grid-cols-[44px_minmax(0,1fr)_42px] items-center gap-3 text-sm text-slate-300">
                            <span>{{ $rating }}★</span>
                            <div class="h-2.5 overflow-hidden rounded-full bg-slate-700">
                                <div class="h-full rounded-full bg-gradient-to-r from-amber-300 via-amber-400 to-orange-400" style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="text-right font-medium text-white">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8 rounded-[28px] border border-stone-200 bg-white p-5 shadow-[0_18px_35px_-28px_rgba(15,23,42,0.45)] sm:p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-600">Pourquoi nous choisir</p>
                    <h2 class="mt-2 text-2xl font-black text-slate-900 sm:text-3xl">Une expérience premium, de la conception à l’installation</h2>
                </div>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-4">
                <div class="rounded-2xl border border-stone-200 bg-gradient-to-br from-stone-50 to-white p-4">
                    <div class="mb-3 text-2xl">🛠️</div>
                    <div class="text-base font-bold text-slate-900">Finition sur mesure</div>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Des solutions customisées qui allient esthétique, solidité et usage quotidien.</p>
                </div>
                <div class="rounded-2xl border border-stone-200 bg-gradient-to-br from-stone-50 to-white p-4">
                    <div class="mb-3 text-2xl">📐</div>
                    <div class="text-base font-bold text-slate-900">Conseil clair</div>
                    <p class="mt-2 text-sm leading-6 text-slate-600">On vous guide avant, pendant et après la commande pour éviter les erreurs de choix.</p>
                </div>
                <div class="rounded-2xl border border-stone-200 bg-gradient-to-br from-stone-50 to-white p-4">
                    <div class="mb-3 text-2xl">⏱️</div>
                    <div class="text-base font-bold text-slate-900">Délai maîtrisé</div>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Un planning sérieux, une exécution rigoureuse, et un suivi du chantier sans ambiguïté.</p>
                </div>
                <div class="rounded-2xl border border-stone-200 bg-gradient-to-br from-stone-50 to-white p-4">
                    <div class="mb-3 text-2xl">✅</div>
                    <div class="text-base font-bold text-slate-900">Engagement vérifié</div>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Chaque témoignage est lié à un vrai projet et publié avec le consentement explicite du client.</p>
                </div>
            </div>
        </div>

        <div class="mb-6 flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-600">Témoignages</p>
                <h2 class="mt-2 text-2xl font-black text-slate-900 sm:text-3xl">Ce que disent nos clients</h2>
            </div>
            <div class="hidden items-center gap-2 rounded-full border border-stone-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 sm:flex">
                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                Vérifiés & consentis
            </div>
        </div>

        <div class="mb-6 overflow-x-auto pb-2">
            <div class="flex min-w-max gap-4">
                @foreach ($reviews as $review)
                    <article class="w-[320px] shrink-0 overflow-hidden rounded-[30px] border border-stone-200 bg-white shadow-[0_20px_40px_-28px_rgba(15,23,42,0.45)]">
                        @if (! empty($review['photo']))
                            <div class="relative">
                                <img src="{{ asset($review['photo']) }}" alt="Projet de {{ $review['author'] }}" class="h-48 w-full object-cover">
                                @if (($review['verified'] ?? false) || ($review['source'] ?? '') === 'interne')
                                    <span class="absolute right-3 top-3 inline-flex rounded-full border border-emerald-300 bg-emerald-500/90 px-2 py-1 text-[9px] font-semibold uppercase tracking-[0.12em] text-white shadow-sm">Client vérifié</span>
                                @endif
                            </div>
                        @else
                            <div class="flex h-48 items-center justify-center bg-gradient-to-br from-amber-100 via-orange-50 to-stone-100 text-4xl text-amber-500">
                                ★
                            </div>
                        @endif

                        <div class="p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <h3 class="text-base font-bold text-slate-900">{{ $review['author'] }}</h3>
                                    <div class="mt-1 text-xs text-slate-500">{{ $review['city'] }}</div>
                                </div>
                                <div class="text-[10px] uppercase tracking-[0.12em] text-slate-500">{{ $review['sourceLabel'] ?? ucfirst($review['source'] ?? 'client') }}</div>
                            </div>

                            <div class="mt-3 flex items-center gap-1 text-sm text-amber-500" aria-label="Note {{ $review['rating'] }} sur 5">
                                @for ($star = 1; $star <= 5; $star++)
                                    <span>{{ $star <= (int) ($review['rating'] ?? 5) ? '★' : '☆' }}</span>
                                @endfor
                            </div>

                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $review['text'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($reviews as $review)
                <article class="group overflow-hidden rounded-[30px] border border-stone-200 bg-white shadow-[0_20px_40px_-28px_rgba(15,23,42,0.45)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_26px_50px_-24px_rgba(15,23,42,0.6)]">
                    @if (! empty($review['photo']))
                        <div class="relative">
                            <img src="{{ asset($review['photo']) }}" alt="Projet de {{ $review['author'] }}" class="h-56 w-full object-cover transition duration-300 group-hover:scale-[1.03]">
                            <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-slate-950/55 to-transparent"></div>
                            @if (($review['verified'] ?? false) || ($review['source'] ?? '') === 'interne')
                                <span class="absolute right-4 top-4 inline-flex rounded-full border border-emerald-300 bg-emerald-500/90 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-white shadow-sm">Client vérifié</span>
                            @endif
                        </div>
                    @else
                        <div class="relative flex h-56 items-center justify-center bg-gradient-to-br from-amber-100 via-orange-50 to-stone-100 text-4xl text-amber-500">
                            ★
                            @if (($review['verified'] ?? false) || ($review['source'] ?? '') === 'interne')
                                <span class="absolute right-4 top-4 inline-flex rounded-full border border-emerald-300 bg-emerald-500/90 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-white shadow-sm">Client vérifié</span>
                            @endif
                        </div>
                    @endif

                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">{{ $review['author'] }}</h3>
                                <div class="mt-1 text-sm text-slate-500">{{ $review['city'] }}</div>
                            </div>
                            <div class="rounded-full border border-stone-200 bg-stone-50 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-600">
                                {{ $review['sourceLabel'] ?? ucfirst($review['source'] ?? 'client') }}
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <div class="flex items-center gap-1 text-base text-amber-500" aria-label="Note {{ $review['rating'] }} sur 5">
                                @for ($star = 1; $star <= 5; $star++)
                                    <span>{{ $star <= (int) ($review['rating'] ?? 5) ? '★' : '☆' }}</span>
                                @endfor
                            </div>
                            @if (($review['project'] ?? null))
                                <span class="text-[10px] uppercase tracking-[0.12em] text-slate-500">{{ \Illuminate\Support\Str::limit((string) $review['project'], 18) }}</span>
                            @endif
                        </div>

                        <p class="mt-4 text-sm leading-6 text-slate-600">{{ $review['text'] }}</p>
                    </div>
                </article>
            @empty
                <div class="rounded-[28px] border border-dashed border-stone-300 bg-white/60 p-8 text-center text-slate-600 md:col-span-2 xl:col-span-3">
                    Aucun avis n’est encore publié. Le système de preuve sociale est prêt à recevoir les premiers retours clients.
                </div>
            @endforelse
        </div>
    </section>
@endsection
