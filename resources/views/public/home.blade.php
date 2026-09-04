@extends('public.layout')

@section('title', config('business.name').' | Menuiserie Aluminium & Vitrerie')
@section('meta_description', config('business.activities'))

@section('content')
    <section class="relative isolate min-h-[560px] overflow-hidden bg-slate-900 text-white sm:min-h-[620px]">
        <div class="hero-slide" style="background-image: linear-gradient(110deg, rgba(15, 23, 42, 0.88), rgba(15, 23, 42, 0.38)), url('https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80');"></div>
        <div class="hero-slide" style="background-image: linear-gradient(110deg, rgba(15, 23, 42, 0.78), rgba(15, 23, 42, 0.42)), url('https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1200&q=80');"></div>
        <div class="hero-slide" style="background-image: linear-gradient(110deg, rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.35)), url('https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1200&q=80');"></div>

        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/90 via-slate-900/75 to-slate-900/30"></div>

        <div class="relative mx-auto grid max-w-6xl gap-8 px-4 py-14 sm:px-6 lg:grid-cols-[1.2fr_0.8fr] lg:items-center lg:px-8 lg:py-20">
            <div>
                <span class="inline-flex rounded-full border border-white/20 bg-white/5 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-200 backdrop-blur-sm">
                    {{ config('business.name') }}
                </span>
                <h1 class="mt-5 text-4xl font-black tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Des ouvertures et mobiliers aluminium conçus pour le climat côtier de Lomé.
                </h1>
                <p class="mt-4 max-w-xl text-base text-slate-200 sm:text-lg">
                    {{ config('business.activities') }} Atelier dirigé par {{ config('business.manager') }}, {{ config('business.expertise') }}.
                </p>

                <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('public.devis') }}" class="inline-flex items-center justify-center rounded-full bg-amber-500 px-5 py-3 text-sm font-semibold text-slate-900 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400">
                        Demander un devis
                    </a>
                    <a href="{{ route('gallery') }}" class="inline-flex items-center justify-center rounded-full border border-white/30 bg-white/5 px-5 py-3 text-sm font-semibold text-white backdrop-blur-sm transition hover:bg-white/10">
                        Voir nos réalisations
                    </a>
                </div>
            </div>

            <div class="rounded-[28px] border border-white/15 bg-white/10 p-5 shadow-2xl shadow-slate-950/30 backdrop-blur-sm">
                <div class="rounded-[24px] bg-gradient-to-br from-amber-200 via-orange-100 to-stone-50 p-5 text-slate-900">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Pourquoi nous choisir ?</p>
                    <ul class="mt-5 space-y-4 text-sm font-medium">
                        <li class="flex items-start gap-3"><span class="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-amber-500"></span> Résistance à la corrosion et à l’air salin du littoral</li>
                        <li class="flex items-start gap-3"><span class="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-amber-500"></span> Finitions précises et assemblages fiables</li>
                        <li class="flex items-start gap-3"><span class="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-amber-500"></span> Accompagnement clair pour les clients locaux et diasporas</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-3xl border border-stone-200 bg-white p-5 shadow-sm">
                <div class="text-3xl font-black text-slate-900">+150</div>
                <div class="mt-2 text-sm text-slate-600">chantiers réalisés</div>
            </div>
            <div class="rounded-3xl border border-stone-200 bg-white p-5 shadow-sm">
                <div class="text-3xl font-black text-slate-900">10 ans</div>
                <div class="mt-2 text-sm text-slate-600">d’expertise</div>
            </div>
            <div class="rounded-3xl border border-stone-200 bg-white p-5 shadow-sm">
                <div class="text-3xl font-black text-slate-900">+40</div>
                <div class="mt-2 text-sm text-slate-600">projets diaspora</div>
            </div>
            <div class="rounded-3xl border border-stone-200 bg-white p-5 shadow-sm">
                <div class="text-3xl font-black text-slate-900">100%</div>
                <div class="mt-2 text-sm text-slate-600">suivi de chantier</div>
            </div>
        </div>
    </section>

    @php
        $reviews = app(\App\ReviewData::class)->mergedReviews();
        $summary = app(\App\ReviewData::class)->summary();
        $featuredReviews = array_values(array_filter($reviews, function (array $review): bool {
            return (int) ($review['rating'] ?? 0) >= 4;
        }));
        $carouselReviews = array_merge($featuredReviews, $featuredReviews);
    @endphp


    <style>
        .testimonial-track {
            width: max-content;
            min-width: 100%;
            animation: marquee 24s linear infinite;
        }

        @media (max-width: 767px) {
            .testimonial-track {
                animation: none;
                overflow-x: auto;
                padding-bottom: 0.25rem;
                scrollbar-width: none;
            }

            .testimonial-track::-webkit-scrollbar {
                display: none;
            }
        }

        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
    </style>

    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Nos services</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">Des solutions pour chaque projet</h2>
            </div>
            <a href="{{ route('services') }}" class="hidden text-sm font-semibold text-amber-600 sm:inline-flex">Découvrir tout</a>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <article class="rounded-3xl border border-stone-200 bg-white p-5 shadow-sm">
                <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-xl">🏗️</div>
                <h3 class="text-xl font-bold text-slate-900">Bâtiment</h3>
                <p class="mt-3 text-sm leading-6 text-slate-600">Portes, fenêtres, baies vitrées, garde-corps, murs-rideaux et façades aluminium pour maisons, villas, bureaux et établissements commerciaux.</p>
                <a href="{{ route('services.detail', ['slug' => 'menuiserie-batiment']) }}" class="mt-5 inline-flex min-h-11 items-center text-sm font-semibold text-amber-600">En savoir plus</a>
            </article>

            <article class="rounded-3xl border border-stone-200 bg-white p-5 shadow-sm">
                <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-xl">🪑</div>
                <h3 class="text-xl font-bold text-slate-900">Mobilier</h3>
                <p class="mt-3 text-sm leading-6 text-slate-600">Cuisines intégrées, comptoirs de vente, tables hautes, armoires et rangements sur mesure pour des espaces fonctionnels et élégants.</p>
                <a href="{{ route('services.detail', ['slug' => 'mobilier-sur-mesure']) }}" class="mt-5 inline-flex min-h-11 items-center text-sm font-semibold text-amber-600">En savoir plus</a>
            </article>
        </div>
    </section>

    <section class="bg-slate-100 py-12">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Notre expertise</p>
                    <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900">Un accompagnement rigoureux, même à distance</h2>
                    <p class="mt-4 text-base leading-7 text-slate-600">
                        Pour les clients installés en Europe ou en Afrique de l’Ouest, nous assurons un suivi simple et transparent : devis détaillé, preuve par l’image, échanges clairs et contact direct via WhatsApp. Chaque étape est documentée pour garantir la confiance avant même la commande.
                    </p>
                    <p class="mt-4 text-base leading-7 text-slate-600">
                        Nous préparons des plans, confirmons les choix de finitions, vérifions les dimensions et coordonnons la livraison ou la pose sur site avec un dialogue constant avec le client.
                    </p>
                    <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-slate-800">
                        Atelier dirigé par {{ config('business.manager') }}, {{ config('business.expertise') }}.
                    </div>
                </div>

                <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                    <div class="space-y-4">
                        <div class="rounded-2xl bg-amber-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">1. Devis clair</div>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Nous détaillons les matériaux, le design et la main d’œuvre pour éviter toute ambiguïté.</p>
                        </div>
                        <div class="rounded-2xl bg-amber-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">2. Preuves par l’image</div>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Chaque étape est documentée par des photos de référence, de fabrication et de pose.</p>
                        </div>
                        <div class="rounded-2xl bg-amber-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">3. Contact direct</div>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Une communication rapide par WhatsApp permet de valider les décisions sans friction.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-stone-100 py-12">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Portfolio</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">Projets publiés</h2>
            </div>

            @if ($featuredProjects->isEmpty())
                <div class="rounded-3xl border border-dashed border-stone-300 bg-white p-6 text-sm text-slate-500">
                    Aucune réalisation publique n’est encore disponible.
                </div>
            @else
                <div class="grid gap-4 md:grid-cols-3">
                    @foreach ($featuredProjects as $project)
                        @php $images = is_array($project->images) ? $project->images : []; @endphp
                        <article class="overflow-hidden rounded-3xl border border-stone-200 bg-white shadow-sm">
                            @if (!empty($images))
                                @php $imageUrl = preg_match('/^https?:\/\//', $images[0]) ? $images[0] : Storage::disk('public')->url($images[0]); @endphp
                                <img src="{{ $imageUrl }}" alt="{{ $project->titre }}" loading="lazy" class="h-52 w-full object-cover" />
                            @else
                                <div class="flex h-52 items-center justify-center bg-gradient-to-br from-amber-100 to-stone-200 text-sm font-semibold text-slate-700">
                                    {{ $project->titre }}
                                </div>
                            @endif
                            <div class="p-4">
                                <div class="mb-2 flex items-center justify-between gap-2">
                                    <span class="rounded-full bg-amber-100 px-2 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-amber-700">
                                        {{ $project->categorie }}
                                    </span>
                                    <span class="text-xs text-slate-500">{{ $project->ville }}, {{ $project->pays }}</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900">{{ $project->titre }}</h3>
                                <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-600">{{ $project->description }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="rounded-3xl bg-slate-900 px-5 py-8 text-center text-white shadow-xl shadow-slate-900/10 sm:px-8">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-300">Tunnel de conversion</p>
            <h2 class="mt-3 text-2xl font-bold sm:text-3xl">Un projet ? Demandez votre devis dès maintenant.</h2>
            <p class="mx-auto mt-3 max-w-2xl text-sm text-slate-300 sm:text-base">
                Recevez une réponse rapide, obtenez un accompagnement clair et commencez sereinement votre chantier ou votre aménagement.
            </p>
            <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('public.devis') }}" class="inline-flex items-center justify-center rounded-full bg-amber-500 px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-amber-400">Formulaire de devis</a>
                <a href="{{ route('contact') }}" class="inline-flex items-center justify-center rounded-full border border-white/25 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">Nous contacter</a>
            </div>
        </div>
    </section>
@endsection
