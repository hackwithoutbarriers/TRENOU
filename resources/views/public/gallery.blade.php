@extends('public.layout')

@section('title', 'Galerie | TRENOU')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="mb-6 max-w-2xl">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Galerie</p>
            <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Nos réalisations visibles publiquement</h1>
        </div>

        <form method="GET" action="{{ route('gallery') }}" class="mb-8 rounded-3xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="grid gap-3 md:grid-cols-4">
                <label class="text-sm font-medium text-slate-600">
                    <span class="mb-1 block">Catégorie</span>
                    <select name="categorie" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm text-slate-900 focus:border-amber-400 focus:outline-none">
                        <option value="">Toutes</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" {{ request('categorie') === $category ? 'selected' : '' }}>{{ ucfirst($category) }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="text-sm font-medium text-slate-600">
                    <span class="mb-1 block">Ville</span>
                    <select name="ville" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm text-slate-900 focus:border-amber-400 focus:outline-none">
                        <option value="">Toutes</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city }}" {{ request('ville') === $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="text-sm font-medium text-slate-600">
                    <span class="mb-1 block">Pays</span>
                    <select name="pays" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm text-slate-900 focus:border-amber-400 focus:outline-none">
                        <option value="">Tous</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country }}" {{ request('pays') === $country ? 'selected' : '' }}>{{ $country }}</option>
                        @endforeach
                    </select>
                </label>

                <div class="grid gap-2 sm:flex sm:items-end">
                    <button type="submit" class="min-h-11 w-full rounded-2xl bg-amber-500 px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-amber-400 sm:w-auto">
                        Filtrer
                    </button>
                    <a href="{{ route('gallery') }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-2xl border border-stone-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-stone-100 sm:w-auto">
                        Réinitialiser
                    </a>
                </div>
            </div>
        </form>

        @if ($projects->isEmpty())
            <div class="rounded-3xl border border-dashed border-stone-300 bg-white p-8 text-center text-sm text-slate-500">
                Aucun projet ne correspond à ces filtres pour le moment.
            </div>
        @else
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($projects as $project)
                    @php $projectImages = is_array($project->images) ? $project->images : []; @endphp
                    <article class="overflow-hidden rounded-3xl border border-stone-200 bg-white shadow-sm">
                        @if (!empty($projectImages))
                            @php $projectImageUrl = preg_match('/^https?:\/\//', $projectImages[0]) ? $projectImages[0] : Storage::disk(config('filesystems.default'))->url($projectImages[0]); @endphp
                            <img src="{{ $projectImageUrl }}" alt="{{ $project->titre }}" loading="lazy" class="aspect-[4/3] h-auto w-full object-cover" />
                        @else
                            <div class="flex h-64 items-center justify-center bg-gradient-to-br from-amber-100 to-stone-200 text-sm font-semibold text-slate-700">
                                {{ $project->titre }}
                            </div>
                        @endif
                        <div class="p-4">
                            <div class="mb-2 flex items-center justify-between gap-2">
                                <span class="rounded-full bg-amber-100 px-2 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-amber-700">
                                    {{ $project->categorie }}
                                </span>
                                <span class="text-[11px] text-slate-500">{{ $project->ville }}</span>
                            </div>
                            <h2 class="text-xl font-bold text-slate-900">{{ $project->titre }}</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $project->description }}</p>
                            <div class="mt-3 text-xs font-medium uppercase tracking-[0.2em] text-slate-500">{{ $project->pays }}</div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
