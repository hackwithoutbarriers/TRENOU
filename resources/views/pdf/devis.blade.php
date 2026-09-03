<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Devis {{ $devis->numero_devis }}</title>
    <style>
        @page { size: A4; margin: 0; }
        @font-face {
            font-family: 'Roboto';
            src: url('{{ public_path('fonts/roboto/Roboto-Regular.ttf') }}') format('truetype');
            font-weight: 400;
        }
        @font-face {
            font-family: 'Roboto';
            src: url('{{ public_path('fonts/roboto/Roboto-Medium.ttf') }}') format('truetype');
            font-weight: 500;
        }
        @font-face {
            font-family: 'Roboto';
            src: url('{{ public_path('fonts/roboto/Roboto-SemiBold.ttf') }}') format('truetype');
            font-weight: 600;
        }
        @font-face {
            font-family: 'Roboto';
            src: url('{{ public_path('fonts/roboto/Roboto-Bold.ttf') }}') format('truetype');
            font-weight: 700;
        }
        body { margin: 0; color: #183247; font-family: 'Roboto', DejaVu Sans, Arial, sans-serif; font-size: 12px; line-height: 1.4; }
        /*
         * Keep the dimensions in the content box while allowing long
         * descriptions and billing tables to flow onto additional pages.
         */
        .page {
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            width: 202mm;
            min-height: 289mm;
            margin: 4mm;
            padding: 7mm 7mm 4mm;
            border: 1px solid #c8d2da;
        }
        table { width: 100%; border-collapse: collapse; }
        .header,
        .summary,
        .closing { break-inside: avoid; }
        .pricing thead { display: table-header-group; }
        .header { padding-bottom: 10px; border-bottom: 2px solid #e3a522; }
        .header td { vertical-align: top; }
        .logo { width: auto; height: auto; max-width: 66px; max-height: 52px; object-fit: contain; }
        .company { padding-top: 1px; color: #16476b; font-family: Helvetica, Arial, sans-serif; font-size: 18px; font-weight: bold; line-height: 1.15; }
        .company small { display: block; max-width: 290px; margin-top: 6px; color: #506574; font-family: 'Roboto', DejaVu Sans, Arial, sans-serif; font-size: 12px; font-weight: 400; line-height: 1.45; }
        .contact { margin-top: 5px; color: #506574; font-size: 12px; line-height: 1.55; }
        .document { width: 35%; text-align: right; }
        .document h1 { margin: 0 0 8px; color: #16476b; font-family: Helvetica, Arial, sans-serif; font-size: 20px; line-height: 1; letter-spacing: .5px; }
        .document div { color: #506574; font-size: 12px; line-height: 1.65; }
        .section-title { margin: 13px 0 6px; color: #16476b; font-family: Helvetica, Arial, sans-serif; font-size: 13px; font-weight: bold; letter-spacing: .15px; }
        .box { border: 1px solid #b9c8d3; }
        .box td { height: 29px; padding: 6px 8px; border-right: 1px solid #d6e0e6; vertical-align: top; }
        .box tr + tr td { border-top: 1px solid #ccc; }
        .box td:last-child { border-right: 0; }
        .label { color: #607785; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; }
        .box .value { display: block; margin-top: 4px; color: #183247; font-size: 12px; line-height: 1.4; }
        .box .project-description { min-height: 50px; line-height: 1.5; overflow-wrap: anywhere; }
        .pricing { margin-top: 3px; }
        .pricing th { height: 25px; padding: 4px 5px; border: 1px solid #9fb1bf; background: #e9f0f4; color: #16476b; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; text-align: center; }
        .pricing td { height: 22px; padding: 3px 5px; border: 1px solid #c2d0d9; color: #183247; font-size: 12px; }
        .pricing .designation strong { display: block; color: #16476b; font-size: 12px; }
        .pricing .designation small { display: block; margin-top: 2px; color: #607785; font-size: 12px; line-height: 1.25; }
        .pricing .index { width: 6%; text-align: center; }
        .pricing .designation { width: 40%; }
        .pricing .quantity { width: 12%; text-align: center; }
        .pricing .amount { width: 21%; text-align: right; white-space: nowrap; }
        .summary { width: 100%; margin-top: 3px; table-layout: fixed; }
        .summary col.label-column { width: 72%; }
        .summary col.amount-column { width: 28%; }
        .summary td { height: 25px; padding: 4px 7px; border: 1px solid #b9c8d3; font-weight: bold; line-height: 1.2; }
        .summary .label { display: table-cell; width: 72%; text-align: right; }
        .summary .value { display: table-cell; width: 28%; margin: 0; color: #16476b; text-align: right; white-space: nowrap; }
        .summary .total td { height: 30px; background: #16476b; color: #fff; font-size: 12px; }
        .summary .total .value { color: #fff; font-size: 12px; }
        .deposit { margin-top: 8px; padding: 7px 9px; border: 1px solid #e3a522; background: #fff8e7; color: #183247; }
        .deposit strong { color: #16476b; font-size: 12px; }
        .deposit .deposit-amount { float: right; color: #16476b; font-size: 12px; font-weight: bold; }
        .closing { margin-top: auto; padding-top: 16px; }
        .closing td { position: relative; width: 50%; height: 145px; padding: 9px; border: 1px solid #b9c8d3; vertical-align: top; color: #506574; font-size: 12px; }
        .closing strong { display: block; margin-bottom: 8px; color: #16476b; font-family: Helvetica, Arial, sans-serif; font-size: 12px; }
        .signature-line { position: absolute; right: 8px; bottom: 9px; left: 8px; border-bottom: 1px solid #8ea3b1; }
        .stamp { position: absolute; right: 18px; bottom: 22px; width: auto; height: auto; max-width: 64px; max-height: 46px; object-fit: contain; }
        .footer { margin-top: 8px; color: #607785; font-size: 12px; text-align: center; }
        .signature-img { display: block; width: 42mm; height: 16mm; margin: 8px auto 4px; object-fit: contain; }
        @media print {
            .page { page-break-inside: auto; }
            .section-title { break-after: avoid; page-break-after: avoid; }
        }
    </style>
</head>
<body>
    @php
        $lignes = is_array($devis->lignes_facturation) ? $devis->lignes_facturation : [];
        $materiel = collect($lignes)->sum(fn (array $ligne): float => (float) ($ligne['quantite'] ?? 0) * (float) ($ligne['prix_unitaire'] ?? 0));
        $mainDoeuvre = (float) $devis->montant_main_doeuvre;
        $montantTotal = (float) $devis->montant_total;
        $acomptePourcentage = (int) $devis->acompte_requis_pourcentage;
        $montantAcompte = $montantTotal * $acomptePourcentage / 100;
    @endphp
    <div class="page">
        <table class="header">
            <tr>
                <td style="width: 65%;">
                    <table>
                        <tr>
                            <td style="width: 52px;"><img class="logo" src="{{ public_path('images/logo/alu-la-solution-compact.png') }}" alt="Logo"></td>
                            <td>
                                <div class="company">{{ config('business.name') }} <small>{{ config('business.activities') }}</small></div>
                                <div class="contact">{{ config('business.address') }} | Tél. : {{ config('business.phone') }} | {{ config('business.email') }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="document">
                    <h1>DEVIS</h1>
                    <div>N° : {{ $devis->numero_devis }}</div>
                    <div>Date : {{ $devis->created_at->format('d/m/Y') }}</div>
                    <div>Validité : 30 jours</div>
                </td>
            </tr>
        </table>

        <div class="section-title">1. Client &amp; Chantier</div>
        <table class="box">
            <tr>
                <td style="width: 44%;"><span class="label">Nom du client :</span><span class="value">{{ $devis->client_nom }}</span></td>
                <td style="width: 28%;"><span class="label">Téléphone :</span><span class="value">{{ $devis->client_telephone }}</span></td>
                <td><span class="label">Ville / Pays :</span><span class="value">{{ $devis->client_ville ?: '—' }} / {{ $devis->client_pays }}</span></td>
            </tr>
            <tr>
                <td colspan="3"><span class="label">Description du chantier :</span><span class="value project-description">{{ $devis->description_chantier }}</span></td>
            </tr>
        </table>

        <div class="section-title">2. Prestations &amp; Valorisation</div>
        <table class="pricing">
            <thead>
                <tr><th class="index">N°</th><th class="designation">Désignation (Titre / Description)</th><th class="quantity">Quantité</th><th class="amount">Prix Unitaire</th><th class="amount">Montant</th></tr>
            </thead>
            <tbody>
                @foreach ($lignes as $ligne)
                    @php $totalLigne = (float) ($ligne['quantite'] ?? 0) * (float) ($ligne['prix_unitaire'] ?? 0); @endphp
                    <tr><td class="index">{{ $loop->iteration }}</td><td class="designation"><strong>{{ $ligne['designation'] ?? 'Prestation' }}</strong><small>{{ $ligne['description'] ?? '—' }}</small></td><td class="quantity">{{ $ligne['quantite'] ?? 0 }}</td><td class="amount">{{ number_format((float) ($ligne['prix_unitaire'] ?? 0), 0, ',', ' ') }} FCFA</td><td class="amount">{{ number_format($totalLigne, 0, ',', ' ') }} FCFA</td></tr>
                @endforeach
                <tr><td class="index">{{ count($lignes) + 1 }}</td><td class="designation">Main-d’œuvre</td><td class="quantity">1</td><td class="amount">{{ number_format($mainDoeuvre, 0, ',', ' ') }} FCFA</td><td class="amount">{{ number_format($mainDoeuvre, 0, ',', ' ') }} FCFA</td></tr>
                @for ($row = 0; $row < max(0, 8 - count($lignes)); $row++)
                    <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
                @endfor
            </tbody>
        </table>
        <table class="summary">
            <colgroup><col class="label-column"><col class="amount-column"></colgroup>
            <tr><td class="label">Total Prestations</td><td class="value">{{ number_format($materiel, 0, ',', ' ') }} FCFA</td></tr>
            <tr><td class="label">Total Main d'œuvre</td><td class="value">{{ number_format($mainDoeuvre, 0, ',', ' ') }} FCFA</td></tr>
            <tr class="total"><td class="label">Montant Total Devis (Net à Payer)</td><td class="value">{{ number_format((float) $devis->montant_total, 0, ',', ' ') }} FCFA</td></tr>
        </table>

        <div class="deposit">
            <strong>Acompte au démarrage : {{ $acomptePourcentage }} % du montant total</strong>
            <span class="deposit-amount">{{ number_format($montantAcompte, 0, ',', ' ') }} FCFA</span>
        </div>

        <table class="closing">
            <tr>
                <td>
                    <strong>Accord Client</strong>
                    Bon pour accord &amp; signature
                    <div class="signature-line"></div>
                </td>
                <td>
                    <strong>Prestataire</strong>
                    {{ config('business.manager') }}<br>{{ config('business.name') }}
                    @if (config('business.signature_image'))
                        <img class="signature-img" src="{{ public_path(config('business.signature_image')) }}" alt="Signature du prestataire">
                    @endif
                    <div class="signature-line"></div>
                    @if (config('business.stamp_image'))
                        <img class="stamp" src="{{ public_path(config('business.stamp_image')) }}" alt="Cachet du prestataire">
                    @endif
                </td>
            </tr>
        </table>
        <div class="footer">{{ config('business.name') }} — Document commercial établi sur la base des informations communiquées par le client.</div>
    </div>
</body>
</html>
