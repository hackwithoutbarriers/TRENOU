<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Attestation de Travail {{ $attestation->numero_attestation }}</title>
    <style>
        @page { size: A4; margin: 0; }
        html, body { width: 210mm; height: 297mm; margin: 0; }
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
            color: #182b3a;
            font-family: 'Roboto', DejaVu Sans, sans-serif;
            font-size: 12px;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }
        .page {
            box-sizing: border-box;
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 297mm;
            margin: 0;
            padding: 6mm;
            overflow: hidden;
        }

        .border-frame {
            position: relative;
            z-index: 1;
            height: 100%;
            border: 5px double #16476b;
            padding: 4mm;
        }
        .border-inner {
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
            border: 1px solid #8f97a0;
            padding: 5mm 12mm 6mm;
        }
        .border-frame {
            clip-path: polygon(
                14mm 0, calc(100% - 14mm) 0, 100% 14mm,
                100% calc(100% - 14mm), calc(100% - 14mm) 100%, 14mm 100%,
                0 calc(100% - 14mm), 0 14mm
            );
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 92mm;
            opacity: .08;
            transform: translate(-50%, -50%);
        }
        .header {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 9mm;
            flex: 0 0 auto;
            margin-top: 10mm;
            padding: 5mm 9mm 6mm;
            border-bottom: 1px solid #aebbc4;
        }
        .logo {
            display: block;
            width: auto;
            height: auto;
            max-width: 52mm;
            max-height: 34mm;
            object-fit: contain;
            flex: 0 0 auto;
        }
        .company {
            flex: 1;
            min-width: 0;
            padding: 1mm 0;
            color: #182b3a;
            text-align: center;
        }
        .company-name {
            color: #16476b;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 18pt;
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: .3px;
        }
        .company-activity {
            margin: 3mm auto 0;
            max-width: 112mm;
            color: #506574;
            font-size: 11pt;
            line-height: 1.25;
            height: auto;
        }
        .contact {
            width: 100%;
            margin: 2.5mm auto 0;
            max-width: 112mm;
            color: #506574;
            font-size: 8.5pt;
            line-height: 1.3;
            text-align: center;
        }
        .content-area {
            position: relative;
            z-index: 2;
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            width: 100%;
            padding: 3mm 0 4mm;
            min-height: 0;
        }
        .content-inner {
            display: flex;
            flex: 1 1 auto;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            width: 88%;
            margin: 0 auto;
            transform: translateY(-7mm);
        }
        h1 {
            margin: 0 0 12mm;
            font-family: Helvetica, Arial, sans-serif;
            color: #16476b;
            font-size: 24pt;
            font-weight: 700;
            letter-spacing: 1px;
            text-align: center;
        }
        .body {
            width: 100%;
            max-width: none;
            margin: 0;
            color: #273f50;
            font-size: 12px;
            line-height: 2.3;
            letter-spacing: .08px;
            text-align: justify;
            word-wrap: normal;
            overflow-wrap: normal;
        }
        .body strong { color: #182b3a; font-weight: 700; }
        .body p {
            margin: 0;
        }
        .body p + p {
            margin-top: 16mm;
        }
        .date { margin-top: 12px; }
        .signature {
            width: 46%;
            align-self: end;
            margin: 0 0 0 auto;
            text-align: center;
            transform: translateY(-8mm);
        }
        .signature-date {
            margin-bottom: 8mm;
            font-size: 9.5pt;
            line-height: 1.35;
            text-align: center;
        }
        .signature-title { margin-bottom: 12px; font-size: 10pt; }
        .signature-img {
            display: block;
            max-width: 100%;
            height: 19mm;
            object-fit: contain;
            margin: 0 auto;
        }
        .signature-name {
            min-height: 19px;
            padding-top: 4px;
            border-bottom: 1px solid #777;
            font-size: 10pt;
            font-weight: bold;
        }
        .stamp {
            box-sizing: border-box;
            width: 34mm;
            height: 22mm;
            margin: 6px auto 0;
            text-align: center;
        }
        .stamp-placeholder {
            box-sizing: border-box;
            width: 100%;
            height: 100%;
            padding-top: 5px;
            font-size: 6pt;
            line-height: 1.35;
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
        .quote {
            position: relative;
            z-index: 2;
            flex: 0 0 auto;
            width: 100%;
            margin: auto 0 0;
            padding-top: 3mm;
            border-top: 1px solid #999;
            font-size: 7pt;
            line-height: 1.25;
            text-align: center;
        }
        .signature-title,
        .signature-name {
            font-family: Helvetica, Arial, sans-serif;
        }
    </style>
</head>
<body>
    @php
        // Capitalise le mois d'une date Carbon en français ("janvier" -> "Janvier")
        $frDate = function ($date) {
            $mois = $date->translatedFormat('F');
            $moisMaj = mb_strtoupper(mb_substr($mois, 0, 1)) . mb_substr($mois, 1);
            return $date->format('d') . ' ' . $moisMaj . ' ' . $date->format('Y');
        };

        // Nombre de mots en français pour la durée d'apprentissage (1 à 20)
        $chiffresEnLettres = [
            1 => 'Un', 2 => 'Deux', 3 => 'Trois', 4 => 'Quatre', 5 => 'Cinq',
            6 => 'Six', 7 => 'Sept', 8 => 'Huit', 9 => 'Neuf', 10 => 'Dix',
            11 => 'Onze', 12 => 'Douze', 13 => 'Treize', 14 => 'Quatorze', 15 => 'Quinze',
            16 => 'Seize', 17 => 'Dix-sept', 18 => 'Dix-huit', 19 => 'Dix-neuf', 20 => 'Vingt',
        ];
        $dureeEnMois = $attestation->date_debut_apprentissage->diffInMonths($attestation->date_fin_apprentissage);
        $dureeEstEnMois = $dureeEnMois < 12;
        $dureeAnnees = intdiv($dureeEnMois, 12);
        $dureeValeur = $dureeEstEnMois ? $dureeEnMois : $dureeAnnees;
        $dureeLettres = $chiffresEnLettres[$dureeValeur] ?? $dureeValeur;
        $dureeUnite = $dureeEstEnMois
            ? 'mois'
            : ($dureeAnnees === 1 ? 'an' : 'ans');

        // Élision "de" / "d'" devant la spécialisation (ex. "en qualité d'Apprenti...")
        $specialisation = trim($attestation->specialisations);
        $premiereLettre = mb_strtolower(mb_substr($specialisation, 0, 1));
        $article = in_array($premiereLettre, ['a', 'e', 'i', 'o', 'u', 'h', 'y']) ? "d’" : 'de ';
    @endphp

    <div class="page">
        <div class="border-frame">
            <div class="border-inner">

        <img class="watermark" src="{{ public_path('images/logo/alu-la-solution-compact.png') }}" alt="">
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
                <h1>ATTESTATION DE TRAVAIL</h1>
                <div class="body">
                    <p>Je soussigné Monsieur <strong>{{ config('business.manager') }}</strong>, {{ config('business.manager_title') }}, Directeur de l’Etablissement <strong>{{ config('business.name') }}</strong>, atteste que le nommé <strong>{{ $attestation->apprenti_nom_prenom }}</strong>, a servi sous mes ordres en qualité {{ $article }}<strong>{{ $specialisation }}</strong> pendant une durée de <strong>{{ $dureeLettres }} ({{ sprintf('%02d', $dureeValeur) }} {{ $dureeUnite }})</strong> à compter du <strong>{{ $frDate($attestation->date_debut_apprentissage) }}</strong> au <strong>{{ $frDate($attestation->date_fin_apprentissage) }}</strong>.</p>
                    <p>En foi de quoi, je lui délivre cette présente <strong>Attestation de Travail</strong> pour servir et valoir ce que de droit.</p>
                </div>
            </div>

            <div class="signature">
                <div class="signature-date">Fait à Lomé, le <strong>{{ $frDate($attestation->date_delivrance) }}</strong>.</div>
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

        <div class="quote">
            Tout ce que ta main trouve à faire, fais-le avec ta force ; car il n’y a ni œuvre, ni pensée, ni science, ni sagesse dans le séjour des morts là où tu vas.
        </div>
            </div>
        </div>
    </div>
</body>
</html>