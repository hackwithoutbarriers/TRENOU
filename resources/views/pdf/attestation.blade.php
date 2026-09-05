<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Attestation de Travail {{ $serialNumber ?? $attestation->documentNumber('ATT') }}</title>
    <style>
        @page { size: A4; margin: 0; }

        :root {
            --blue: #104482;
            --blue-dark: #0c3563;
            --blue-soft: #eef2f8;
            --gray: #6b7280;
            --gray-dark: #4a5568;
            --ink: #1a202c;
            --ink-soft: #2d3748;
            --rule: #d7dde5;
            --cream: #fffdf9;
        }

        * { box-sizing: border-box; }

        html, body { width: 210mm; height: 297mm; margin: 0; background: var(--cream); }

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

        body {
            color: var(--ink);
            font-family: 'Roboto', 'Helvetica Neue', Helvetica, Arial, 'DejaVu Sans', sans-serif;
            font-size: 12px;
            background: var(--cream);
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        /* ---------- Structure générale ---------- */
        .page {
            box-sizing: border-box;
            position: relative;
            width: 100%;
            height: 297mm;
            margin: 0;
            padding: 9mm;
        }

        /* Cadre d'honneur : double filet + coupes d'angle mitrées */
        .frame {
            position: relative;
            z-index: 1;
            height: 100%;
            border: 2px solid var(--blue);
            clip-path: polygon(
                10mm 0, calc(100% - 10mm) 0, 100% 10mm,
                100% calc(100% - 10mm), calc(100% - 10mm) 100%, 10mm 100%,
                0 calc(100% - 10mm), 0 10mm
            );
        }
        .frame::before {
            content: '';
            position: absolute;
            inset: 2.6mm;
            border: 0.75px solid var(--blue);
            opacity: .5;
            pointer-events: none;
        }
        .frame::after {
            content: '';
            position: absolute;
            inset: 4.6mm;
            border: 0.6px solid var(--rule);
            pointer-events: none;
        }

        /* Petits repères ornementaux sur les coupes d'angle */
        .corner-mark {
            position: absolute;
            z-index: 2;
            width: 2.6mm;
            height: 2.6mm;
            background: var(--blue);
            transform: rotate(45deg);
        }
        .corner-mark.tl { top: 5.4mm; left: 5.4mm; }
        .corner-mark.tr { top: 5.4mm; right: 5.4mm; }
        .corner-mark.bl { bottom: 5.4mm; left: 5.4mm; }
        .corner-mark.br { bottom: 5.4mm; right: 5.4mm; }

        .inner {
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
            padding: 10mm 14mm 9mm;
            overflow: hidden;
        }

        /* ---------- Filigrane ---------- */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 105mm;
            opacity: .045;
            transform: translate(-50%, -50%);
            z-index: 0;
        }
        .watermark-pattern {
            position: absolute;
            inset: 0;
            z-index: 0;
            background-image: repeating-linear-gradient(
                135deg,
                rgba(16, 68, 130, 0.03) 0px,
                rgba(16, 68, 130, 0.03) 1px,
                transparent 1px,
                transparent 7px
            );
        }

        /* ---------- Référence ---------- */
        .ref-line {
            position: relative;
            z-index: 2;
            text-align: right;
            font-size: 7.6pt;
            color: var(--gray);
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-bottom: 2mm;
        }
        .ref-line strong {
            color: var(--blue);
            font-size: 8.4pt;
            letter-spacing: .3px;
            text-transform: none;
        }

        /* ---------- En-tête ---------- */
        .header {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 7mm;
            flex: 0 0 auto;
            padding-bottom: 5mm;
            border-bottom: 1.4px solid var(--blue);
        }
        .header::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: -1.6mm;
            height: .5px;
            background: var(--rule);
        }
        .logo {
            display: block;
            width: auto;
            height: auto;
            max-width: 42mm;
            max-height: 24mm;
            object-fit: contain;
            flex: 0 0 auto;
        }
        .company {
            flex: 1;
            min-width: 0;
            text-align: center;
        }
        .company-name {
            color: var(--blue);
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 16.5pt;
            font-weight: 700;
            letter-spacing: .6px;
            text-transform: uppercase;
            line-height: 1.15;
        }
        .company-activity {
            margin: 2.2mm auto 0;
            max-width: 120mm;
            color: var(--gray-dark);
            font-size: 9.4pt;
            font-style: italic;
            line-height: 1.3;
        }
        .contact {
            width: 100%;
            margin: 2.4mm auto 0;
            max-width: 120mm;
            color: var(--gray);
            font-size: 8pt;
            line-height: 1.35;
            text-align: center;
        }

        /* ---------- Corps ---------- */
        .content-area {
            position: relative;
            z-index: 2;
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            justify-content: center;
            min-height: 0;
        }
        .content-inner {
            width: 88%;
            margin: 0 auto;
        }

        .title-block { text-align: center; margin-bottom: 7.5mm; }
        .kicker {
            color: var(--gray-dark);
            font-size: 8.4pt;
            font-weight: 600;
            letter-spacing: 2.6px;
            text-transform: uppercase;
            margin-bottom: 2.6mm;
        }
        h1 {
            margin: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: var(--blue);
            font-size: 23.5pt;
            font-weight: 700;
            letter-spacing: 1.6px;
            text-transform: uppercase;
        }
        .subtitle {
            margin-top: 2.4mm;
            color: var(--gray-dark);
            font-size: 9.2pt;
            font-style: italic;
        }
        .title-rule {
            position: relative;
            width: 38mm;
            height: 1.4px;
            background: var(--blue);
            margin: 4.8mm auto 0;
        }
        .title-rule::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 2.6mm;
            height: 2.6mm;
            background: var(--blue);
            transform: translate(-50%, -50%) rotate(45deg);
        }
        .title-rule::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 9mm;
            height: 9mm;
            border: .6px solid var(--blue);
            border-radius: 50%;
            opacity: .35;
            transform: translate(-50%, -50%);
        }

        .body {
            width: 100%;
            color: var(--ink-soft);
            font-size: 11.6px;
            line-height: 1.95;
            letter-spacing: .05px;
            text-align: justify;
        }
        .body strong { color: var(--blue); font-weight: 600; }
        .body p { margin: 0; }
        .body p + p { margin-top: 6mm; }

        /* Encart de mise en valeur : identité / durée / période */
        .details-box {
            margin: 6.5mm 0;
            padding: 5.5mm 7mm;
            background: var(--blue-soft);
            border-left: 2.4px solid var(--blue);
        }
        .details-row.main { margin-bottom: 4mm; }
        .details-label {
            display: block;
            font-size: 7.6pt;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--gray-dark);
            margin-bottom: 1.2mm;
        }
        .details-value {
            display: block;
            font-size: 12.5pt;
            font-weight: 700;
            color: var(--blue-dark);
        }
        .details-value.name { font-size: 14pt; }
        .details-grid { display: flex; }
        .details-cell { flex: 1; }
        .details-cell + .details-cell { margin-left: 8mm; }
        .details-cell .details-value { font-size: 10.6pt; font-weight: 600; color: var(--ink); }

        /* ---------- Clôture / Signature ---------- */
        .closing-row {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 9mm;
        }
        .place-date { flex: 0 0 auto; max-width: 55mm; }
        .place-date-label { display: block; color: var(--gray-dark); font-size: 9pt; }
        .place-date-value { display: block; margin-top: 1.4mm; color: var(--ink); font-size: 11pt; font-weight: 700; }

        .signature-block { flex: 0 0 auto; width: 52mm; text-align: center; }
        .signature-title {
            margin-bottom: 9px;
            color: var(--gray-dark);
            font-size: 9pt;
            font-weight: 600;
            letter-spacing: .5px;
            text-transform: uppercase;
        }
        .signature-img {
            display: block;
            max-width: 100%;
            height: 16mm;
            object-fit: contain;
            margin: 0 auto;
        }
        .signature-name {
            min-height: 16px;
            margin-top: 2mm;
            padding-top: 2.6mm;
            border-top: 1px solid var(--blue);
            color: var(--ink);
            font-size: 10pt;
            font-weight: 700;
        }
        .stamp {
            box-sizing: border-box;
            width: 27mm;
            height: 27mm;
            margin: 6mm auto 0;
            border: 1px dashed var(--gray);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2mm;
            text-align: center;
            transform: rotate(-8deg);
        }
        .stamp-placeholder {
            font-size: 6pt;
            line-height: 1.35;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: .3px;
        }
        .stamp img {
            display: block;
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: 100%;
            margin: 0 auto;
            object-fit: contain;
        }

        /* ---------- Pied de page ---------- */
        .quote {
            position: relative;
            z-index: 2;
            flex: 0 0 auto;
            width: 100%;
            margin-top: 8mm;
            padding-top: 3mm;
            border-top: 0.6px solid var(--rule);
            color: var(--gray);
            font-size: 6.8pt;
            font-style: italic;
            line-height: 1.3;
            text-align: center;
        }

        .signature-title,
        .signature-name,
        .company-name,
        h1 {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }
    </style>
</head>
<body>
    @php
        $moisFrancais = [
            1 => 'janvier',
            2 => 'février',
            3 => 'mars',
            4 => 'avril',
            5 => 'mai',
            6 => 'juin',
            7 => 'juillet',
            8 => 'août',
            9 => 'septembre',
            10 => 'octobre',
            11 => 'novembre',
            12 => 'décembre',
        ];

        $frDate = function ($date) use ($moisFrancais) {
            if ($date === null) {
                return '—';
            }

            $mois = $moisFrancais[$date->month];

            return $date->format('d') . ' ' . mb_strtoupper(mb_substr($mois, 0, 1)) . mb_substr($mois, 1) . ' ' . $date->format('Y');
        };

        // Nombre de mots en français pour la durée d'apprentissage (1 à 20)
        $chiffresEnLettres = [
            1 => 'Un', 2 => 'Deux', 3 => 'Trois', 4 => 'Quatre', 5 => 'Cinq',
            6 => 'Six', 7 => 'Sept', 8 => 'Huit', 9 => 'Neuf', 10 => 'Dix',
            11 => 'Onze', 12 => 'Douze', 13 => 'Treize', 14 => 'Quatorze', 15 => 'Quinze',
            16 => 'Seize', 17 => 'Dix-sept', 18 => 'Dix-huit', 19 => 'Dix-neuf', 20 => 'Vingt',
        ];
        $dureeEnMois = (float) $attestation->date_debut_apprentissage->diffInMonths($attestation->date_fin_apprentissage);
        $dureeEstEnMois = $dureeEnMois <= 12;
        $dureeAnnees = (int) floor($dureeEnMois / 12);
        $dureeValeur = $dureeEstEnMois ? max(1, (int) round($dureeEnMois)) : $dureeAnnees;
        $dureeLettres = $chiffresEnLettres[$dureeValeur] ?? $dureeValeur;
        $dureeUnite = $dureeEstEnMois
            ? 'mois'
            : ($dureeAnnees === 1 ? 'an' : 'ans');
    @endphp

    <div class="page">
        <div class="frame">
            <span class="corner-mark tl"></span>
            <span class="corner-mark tr"></span>
            <span class="corner-mark bl"></span>
            <span class="corner-mark br"></span>

            <div class="inner">

                <img class="watermark" src="{{ public_path('images/logo/alu-la-solution-compact.png') }}" alt="">
                <div class="watermark-pattern"></div>

                <div class="ref-line">Réf. N° <strong>{{ $serialNumber ?? $attestation->documentNumber('ATT') }}</strong></div>

                <div class="header">
                    <img class="logo" src="{{ public_path('images/logo/alu-la-solution-compact.png') }}" alt="Logo {{ config('business.name') }}">
                    <div class="company">
                        <div class="company-name">{{ config('business.name') }}</div>
                        <div class="company-activity">{{ config('business.activities') }}</div>
                        <div class="contact">
                            {{ config('business.address') }} — Tél. : {{ config('business.phone') }}<br>
                            E-mail : {{ config('business.email') }}
                        </div>
                    </div>
                </div>

                <div class="content-area">
                    <div class="content-inner">
                        <div class="title-block">
                            <div class="kicker">Attestation Officielle</div>
                            <h1>Attestation de Travail</h1>
                            <div class="subtitle">Délivrée en complément du Certificat de Fin d'Apprentissage</div>
                            <div class="title-rule"></div>
                        </div>
                        <div class="body">
                            <p class="lead">Je soussigné Monsieur <strong>{{ config('business.manager') }}</strong>, {{ config('business.manager_title') }}, Directeur de l'Etablissement <strong>{{ config('business.name') }}</strong>, atteste par la présente que :</p>

                            <div class="details-box">
                                <div class="details-row main">
                                    <span class="details-label">Nom &amp; Prénoms</span>
                                    <span class="details-value name">{{ $attestation->apprenti_nom_prenom }}</span>
                                </div>
                                <div class="details-grid">
                                    <div class="details-cell">
                                        <span class="details-label">Période de formation</span>
                                        <span class="details-value">{{ $frDate($attestation->date_debut_apprentissage) }} au {{ $frDate($attestation->date_fin_apprentissage) }}</span>
                                    </div>
                                    <div class="details-cell">
                                        <span class="details-label">Durée</span>
                                        <span class="details-value">{{ $dureeLettres }} ({{ sprintf('%02d', $dureeValeur) }} {{ $dureeUnite }})</span>
                                    </div>
                                </div>
                            </div>

                            <p>a servi sous mes ordres avec assiduité et rigueur durant toute la période susmentionnée, dans le cadre de sa formation en apprentissage au sein de notre établissement.</p>
                            <p>En foi de quoi, je lui délivre cette présente <strong>Attestation de Travail</strong> pour servir et valoir ce que de droit.</p>
                        </div>
                    </div>
                </div>

                <div class="closing-row">
                    <div class="place-date">
                        <span class="place-date-label">Fait à Lomé, le</span>
                        <span class="place-date-value">{{ $frDate($attestation->date_delivrance) }}</span>
                    </div>

                    <div class="signature-block">
                        <div class="signature-title">Le Directeur</div>
                        @if(config('business.signature_image'))
                            <img class="signature-img" src="{{ public_path(config('business.signature_image')) }}" alt="Signature">
                        @endif
                        <div class="signature-name">{{ config('business.manager') }}</div>
                        <div class="stamp">
                            @if(config('business.stamp_image'))
                                <img src="{{ public_path(config('business.stamp_image')) }}" alt="Cachet">
                            @else
                                <div class="stamp-placeholder">ETS ALU<br>LA SOLUTION<br>Cachet officiel</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="quote">
                    Tout ce que ta main trouve à faire, fais-le avec ta force ; car il n'y a ni œuvre, ni pensée, ni science, ni sagesse dans le séjour des morts là où tu vas.
                </div>

            </div>
        </div>
    </div>
</body>
</html>