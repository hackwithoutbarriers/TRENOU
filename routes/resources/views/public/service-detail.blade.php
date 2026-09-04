@extends('public.layout')

@section('title', $service['title'] . ' | TRENOU')
@section('meta_description', $service['summary'])

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Service</p>
            <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">{{ $service['title'] }}</h1>
            <p class="mt-4 text-base leading-7 text-slate-600">{{ $service['summary'] }}</p>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-bold text-slate-900">Ce que nous proposons</h2>
                <p class="mt-4 text-sm leading-7 text-slate-600">{{ $service['description'] }}</p>

                <ul class="mt-5 grid gap-3 sm:grid-cols-2">
                    @foreach ($service['highlights'] as $item)
                        <li class="flex items-center gap-3 rounded-2xl bg-amber-50 px-4 py-3 text-sm font-medium text-slate-700">
                            <span class="inline-block h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900">Notre méthode</h2>
                <ul class="mt-4 space-y-3">
                    @foreach ($service['process'] as $step)
                        <li class="flex gap-3 text-sm leading-6 text-slate-600">
                            <span class="mt-1 inline-flex h-6 w-6 flex-none items-center justify-center rounded-full bg-amber-100 text-[10px] font-bold text-amber-700">{{ $loop->iteration }}</span>
                            <span>{{ $step }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('public.devis') }}" class="inline-flex items-center justify-center rounded-full bg-amber-500 px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-amber-400">
                Demander un devis
            </a>
            <a href="{{ route('services') }}" class="inline-flex items-center justify-center rounded-full border border-stone-200 bg-stone-50 px-5 py-3 text-sm font-semibold text-slate-800 transition hover:bg-stone-100">
                Voir tous les services
            </a>
        </div>
    </section>
@endsection
