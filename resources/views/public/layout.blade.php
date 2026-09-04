<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ trim($__env->yieldContent('meta_description')) ?: config('business.activities') }}">
        <meta name="robots" content="index,follow">
        <meta property="og:type" content="website">
        <meta property="og:title" content="{{ trim($__env->yieldContent('title')) ?: config('business.name') }}">
        <meta property="og:description" content="{{ trim($__env->yieldContent('meta_description')) ?: config('business.activities') }}">
        <meta property="og:site_name" content="{{ config('business.name') }}">
        <meta property="og:locale" content="fr_TG">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ trim($__env->yieldContent('title')) ?: config('business.name') }}">
        <meta name="twitter:description" content="{{ trim($__env->yieldContent('meta_description')) ?: config('business.activities') }}">
        <link rel="canonical" href="{{ url()->current() }}">
        <link rel="icon" href="{{ asset('images/logo/alu-la-solution-favicon.webp') }}" type="image/webp">
        <link rel="alternate icon" href="{{ asset('images/logo/alu-la-solution-favicon.png') }}" type="image/png">
        <title>{{ trim($__env->yieldContent('title')) ?: config('business.name') }}</title>

        @php
            $reviewSchema = app(\App\ReviewData::class)->jsonLd();
            $schema = [
                '@context' => 'https://schema.org',
                '@type' => 'LocalBusiness',
                'name' => config('business.name'),
                'description' => config('business.activities'),
                'url' => url('/'),
                'telephone' => config('business.phone'),
                'email' => config('business.email'),
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => config('business.address'),
                    'addressLocality' => 'Lomé',
                    'addressCountry' => 'TG',
                ],
                'areaServed' => ['Lomé', 'Togo'],
                'priceRange' => '$$$',
                'sameAs' => [
                    'https://wa.me/'.config('business.phone_digits'),
                ],
                'aggregateRating' => $reviewSchema['aggregateRating'] ?? null,
                'review' => $reviewSchema['review'] ?? [],
            ];
        @endphp
        <script type="application/ld+json">
            @json($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        </script>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-stone-50 text-slate-900 antialiased">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(251,191,36,0.15),_transparent_35%)]">
            <header class="sticky top-0 z-50 border-b border-slate-200/70 bg-white/75 backdrop-blur-xl shadow-[0_10px_30px_rgba(15,23,42,0.04)]">
                <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
                    <a href="{{ route('home') }}" class="flex min-w-0 flex-1 items-center gap-2 text-lg font-black tracking-tight text-slate-900">
                        <picture class="shrink-0">
                            <source srcset="{{ asset('images/logo/alu-la-solution-compact.webp') }}" type="image/webp">
                            <img src="{{ asset('images/logo/alu-la-solution-compact.png') }}" alt="{{ config('business.name') }}" width="44" height="44" class="h-11 w-11 rounded-xl object-contain shadow-sm" />
                        </picture>
                        <span class="truncate">{{ config('business.name') }}</span>
                    </a>

                    <div class="hidden items-center gap-6 text-sm font-medium text-slate-600 lg:flex">
                        <a href="{{ route('home') }}" class="inline-flex min-h-11 items-center transition hover:text-amber-600">Accueil</a>
                        <a href="{{ route('services') }}" class="inline-flex min-h-11 items-center transition hover:text-amber-600">Services</a>
                        <a href="{{ route('gallery') }}" class="inline-flex min-h-11 items-center transition hover:text-amber-600">Galerie</a>
                        <a href="{{ route('public.devis') }}" class="inline-flex min-h-11 items-center transition hover:text-amber-600">Devis</a>
                        <a href="{{ route('contact') }}" class="inline-flex min-h-11 items-center transition hover:text-amber-600">Contact</a>
                        @auth
                            <a href="{{ url('/admin') }}" class="inline-flex items-center rounded-full bg-slate-900 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-white transition hover:bg-slate-700">
                                Tableau de Bord Admin
                            </a>
                        @endauth
                    </div>

                    <div class="flex items-center gap-2 sm:gap-3">
                        <a href="{{ route('public.devis') }}" class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-full bg-amber-500 px-2.5 py-2 text-xs font-semibold text-slate-900 shadow-sm shadow-amber-500/20 transition hover:bg-amber-400 sm:px-4 sm:text-sm">
                            Demander un devis
                        </a>

                        <button
                            id="mobile-menu-toggle"
                            type="button"
                            class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white/90 text-slate-700 shadow-sm lg:hidden"
                            aria-label="Ouvrir le menu"
                            aria-controls="mobile-menu"
                            aria-expanded="false"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                <path d="M4 7h16M4 12h16M4 17h16" />
                            </svg>
                        </button>
                    </div>
                </nav>

                <div id="mobile-menu" class="hidden border-t border-slate-200 bg-white/90 lg:hidden">
                    <div class="mx-auto flex max-w-6xl flex-col gap-2 px-4 py-4 sm:px-6">
                        <a href="{{ route('home') }}" class="inline-flex min-h-11 items-center rounded-2xl px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 hover:text-amber-600" @if (request()->routeIs('home')) aria-current="page" @endif>Accueil</a>
                        <a href="{{ route('services') }}" class="inline-flex min-h-11 items-center rounded-2xl px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 hover:text-amber-600" @if (request()->routeIs('services') || request()->routeIs('services.detail')) aria-current="page" @endif>Services</a>
                        <a href="{{ route('gallery') }}" class="inline-flex min-h-11 items-center rounded-2xl px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 hover:text-amber-600" @if (request()->routeIs('gallery')) aria-current="page" @endif>Galerie</a>
                        <a href="{{ route('public.devis') }}" class="inline-flex min-h-11 items-center rounded-2xl px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 hover:text-amber-600" @if (request()->routeIs('public.devis')) aria-current="page" @endif>Devis</a>
                        <a href="{{ route('contact') }}" class="inline-flex min-h-11 items-center rounded-2xl px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 hover:text-amber-600" @if (request()->routeIs('contact')) aria-current="page" @endif>Contact</a>
                        @auth
                            <a href="{{ url('/admin') }}" class="mt-1 inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700">
                                Tableau de Bord Admin
                            </a>
                        @endauth
                    </div>
                </div>
            </header>

            <main class="pb-20">
                @yield('content')
            </main>

            <footer class="border-t border-slate-200 bg-white/80 backdrop-blur-sm">
                <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-3 px-4 py-4 text-xs text-slate-500 sm:flex-row sm:px-6 lg:px-8">
                    <div>
                        <p>© {{ date('Y') }} {{ config('business.name') }}. {{ config('business.activities') }}</p>
                        <p class="mt-1 min-w-0 break-words text-[11px]">{{ config('business.address') }} · {{ config('business.phone') }} · <a href="mailto:{{ config('business.email') }}" class="break-words">{{ config('business.email') }}</a></p>
                    </div>
                    <div class="flex items-center gap-4 text-[11px] uppercase tracking-[0.16em] text-slate-400">
                        <a href="{{ route('contact') }}" class="inline-flex min-h-11 items-center transition hover:text-slate-600">Contact</a>
                        <a href="{{ url('/admin') }}" class="inline-flex min-h-11 items-center transition hover:text-slate-600">Accès pro</a>
                    </div>
                </div>
            </footer>

            @include('public.partials.whatsapp-button')
        </div>
    </body>
</html>
