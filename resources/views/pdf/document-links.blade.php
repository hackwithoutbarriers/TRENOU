<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Documents {{ $attestation->serialNumber() }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-100 px-4 py-10 text-slate-900">
    <main class="mx-auto max-w-2xl rounded-3xl bg-white p-6 shadow-xl sm:p-10">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-600">Documents prêts</p>
        <h1 class="mt-3 text-2xl font-bold sm:text-3xl">Documents de {{ $attestation->apprenti_nom_prenom }}</h1>
        <p class="mt-3 text-sm leading-6 text-slate-600">
            Les deux documents utilisent le même numéro de série : <strong>{{ $attestation->serialNumber() }}</strong>.
        </p>

        <div class="mt-8 grid gap-4 sm:grid-cols-2">
            <a href="{{ $links['certificate'] }}" target="_blank" rel="noopener noreferrer" class="rounded-2xl bg-slate-900 px-5 py-4 text-center font-semibold text-white transition hover:bg-slate-700">
                Télécharger le certificat
            </a>
            <a href="{{ $links['attestation'] }}" target="_blank" rel="noopener noreferrer" class="rounded-2xl bg-amber-500 px-5 py-4 text-center font-semibold text-slate-950 transition hover:bg-amber-400">
                Télécharger l’attestation
            </a>
        </div>
    </main>
</body>
</html>
