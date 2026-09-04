<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Certificat de fin d’apprentissage {{ $attestation->numero_attestation }}</title>
    <style>
        @page { size: A4 landscape; margin: 0; }
        * { box-sizing: border-box; }
        html, body { width: 297mm; height: 210mm; margin: 0; }

        @font-face { font-family: 'Roboto'; src: url('{{ public_path('fonts/roboto/Roboto-Regular.ttf') }}') format('truetype'); font-weight: 400; }
        @font-face { font-family: 'Roboto'; src: url('{{ public_path('fonts/roboto/Roboto-Medium.ttf') }}') format('truetype'); font-weight: 500; }
        @font-face { font-family: 'Roboto'; src: url('{{ public_path('fonts/roboto/Roboto-Bold.ttf') }}') format('truetype'); font-weight: 700; }
        @font-face { font-family: 'Helvetica'; src: local('Arial'); }

        body {
            margin: 0;
            color: #1A202C;
            font-family: 'Roboto', Arial, sans-serif;
            font-size: 11pt;
            background: #e9e6df;
        }

        /* ===== Feuille & cadre d'honneur ===== */
        .certificate {
            position: relative;
            width: 297mm;
            height: 210mm;
            padding: 7mm;
            background: #fdfcf8;
        }

        .frame-outer {
            position: relative;
            height: 100%;
            border: 2.5px solid #104482;
            padding: 2.5mm;
        }

        .frame-inner {
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
            border: 1px solid #104482;
            padding: 7mm 16mm 9mm;
            overflow: hidden;
        }

        /* Coins ornementaux — CSS pur, sans SVG, compatible dompdf */
        .corner { position: absolute; width: 30px; height: 30px; z-index: 2; }
        .corner .c1 { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border-top: 3px solid #104482; border-left: 3px solid #104482; }
        .corner .c2 { position: absolute; top: 6px; left: 6px; width: 100%; height: 100%; border-top: 1px solid #9fb3cf; border-left: 1px solid #9fb3cf; }
        .corner.tl { top: -1px; left: -1px; }
        .corner.tr { top: -1px; right: -1px; transform: scaleX(-1); }
        .corner.bl { bottom: -1px; left: -1px; transform: scaleY(-1); }
        .corner.br { bottom: -1px; right: -1px; transform: scale(-1, -1); }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 90mm;
            opacity: .05;
            transform: translate(-50%, -50%);
            z-index: 0;
        }

        .cert-number {
            position: absolute;
            top: 7mm;
            right: 12mm;
            z-index: 3;
            padding: 1.5mm 4mm;
            border: 1px solid #104482;
            background: rgba(255, 255, 255, .65);
            color: #4A5568;
            font-size: 8.5pt;
            letter-spacing: .5px;
        }

        /* ===== En-tête ===== */
        .header { position: relative; z-index: 1; text-align: center; }
        .logo-frame { height: 60px; margin: 0 auto 2mm; text-align: center; line-height: 60px; }
        .logo-frame img { width: auto; height: auto; max-width: 90px; max-height: 60px; object-fit: contain; vertical-align: middle; }

        .company .name { font-family: 'Helvetica', Arial, sans-serif; font-size: 17pt; font-weight: 700; color: #104482; letter-spacing: 1.5px; line-height: 1.1; text-transform: uppercase; }
        .company small { display: block; font-size: 10pt; font-style: italic; font-weight: normal; color: #4A5568; margin-top: 1.5mm; line-height: 1.25; }

        .ornament-divider { position: relative; margin: 3mm auto 2mm; width: 60%; }
        .ornament-divider hr { border: none; border-top: 1px solid #104482; margin: 0; }
        .ornament-divider .diamond { position: absolute; top: 50%; left: 50%; width: 6px; height: 6px; background: #104482; transform: translate(-50%, -50%) rotate(45deg); }

        .contact { font-size: 9.5pt; color: #4A5568; line-height: 1.2; }

        h1 {
            margin: 4mm 0 1.5mm;
            font-family: 'Helvetica', Georgia, 'Times New Roman', serif;
            font-size: 25pt;
            font-weight: 700;
            letter-spacing: 2.5px;
            color: #104482;
            text-transform: uppercase;
        }
        .subtitle { font-size: 10.5pt; font-style: italic; color: #6B7280; }

        /* ===== Corps ===== */
        .content {
            position: absolute;
            z-index: 1;
            top: 58mm;
            right: 0;
            bottom: 35mm;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .identity-grid { width: 100%; margin: 0 auto; border-collapse: collapse; }
        .identity-grid > tbody > tr > td { padding: 0 6mm; vertical-align: middle; }

        .photo-cell { width: 22%; text-align: center; }
        .photo-frame {
            display: flex; width: 32mm; height: 32mm; margin: 0 auto 2.5mm; align-items: center; justify-content: center;
            border: 1.5px dashed #9fb3cf; text-align: center; font-size: 9pt; color: #8a8a8a; background: #fff;
        }
        .photo-frame.has-photo { border: 1.5px solid #104482; padding: 1.5mm; }
        .photo-frame img { display: block; width: 100%; height: 100%; object-fit: contain; }
        .photo-frame span { display: block; }
        .photo-caption { font-size: 9pt; color: #4A5568; }
        .photo-caption strong { display: block; margin-top: 1mm; color: #1A202C; }

        .cert-text { font-size: 11pt; line-height: 1.85; text-align: left; }
        .cert-text p { margin: 0 0 2.5mm; }

        .apprentice-name {
            position: relative;
            margin: 3mm 0;
            text-align: center;
            font-family: 'Helvetica', Georgia, 'Times New Roman', serif;
            font-size: 19pt;
            font-weight: 700;
            letter-spacing: 1px;
            color: #104482;
            text-transform: uppercase;
        }

        .specialty-wrap { text-align: center; }
        .specialty-badge {
            display: inline-block;
            margin: 1.5mm 0 2.5mm;
            padding: 1.5mm 6mm;
            border-top: 1px solid #104482;
            border-bottom: 1px solid #104482;
            font-size: 11pt;
            font-weight: 700;
            color: #4A5568;
            text-align: center;
        }

        .closing { margin-top: 2mm; font-size: 10.5pt; color: #4A5568; text-align: center; font-style: italic; }

        /* ===== Pied de page ===== */
        .footer {
            position: absolute;
            z-index: 1;
            left: 0;
            right: 0;
            bottom: 0;
            padding-top: 4mm;
            border-top: 1px solid #104482;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
        }

        .footer-col { font-size: 10pt; }
        .footer-date { position: relative; left: 4mm; bottom: 3mm; width: 30%; color: #1A202C; }
        .footer-date strong { color: #104482; }

        .footer-stamp { width: 26%; text-align: center; }
        .stamp {
            display: inline-block; width: 24mm; height: 24mm; line-height: 24mm;
            text-align: center; font-size: 8.5pt; color: #8a8a8a; border: 1px dashed #b9c2cc; border-radius: 50%;
        }
        .stamp.has-stamp { line-height: normal; border: none; }
        .stamp img { display: block; width: 100%; height: 100%; max-width: 100%; max-height: 100%; object-fit: contain; }

        .footer-signature { width: 34%; text-align: center; }
        .director-title { font-weight: 700; color: #104482; margin-bottom: 1.5mm; font-size: 10pt; }
        .signature-space { height: 11mm; display: flex; align-items: flex-end; justify-content: center; }
        .signature-img { display: block; max-width: 28mm; max-height: 11mm; object-fit: contain; margin: 0 auto; }
        .signature-line { width: 32mm; height: 1px; margin: 0 auto; border-bottom: 1px solid #104482; }
        .director-name { font-weight: 700; margin-top: 1.5mm; color: #1A202C; font-size: 10pt; }
    </style>
</head>
@php
    $photoRelativePath = (string) $attestation->photo_profil;
    $photoDataUri = null;
    if (preg_match('/\A[a-zA-Z0-9][a-zA-Z0-9\/_-]*\.(?:jpg|jpeg|png|webp)\z/i', $photoRelativePath)) {
        $photoDisk = Storage::disk(config('filesystems.default'));
        if ($photoDisk->exists($photoRelativePath)) {
            $photoContents = $photoDisk->get($photoRelativePath);
            $photoMimeType = $photoDisk->mimeType($photoRelativePath) ?: 'application/octet-stream';
            $photoDataUri = 'data:'.$photoMimeType.';base64,'.base64_encode($photoContents);
        }
    }
@endphp
<body>
    @php
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
        $dureeUnite = $dureeEstEnMois ? 'mois' : ($dureeAnnees === 1 ? 'an' : 'ans');
    @endphp

    <div class="certificate">
        <div class="frame-outer">
            <div class="frame-inner">
                <div class="corner tl"><div class="c1"></div><div class="c2"></div></div>
                <div class="corner tr"><div class="c1"></div><div class="c2"></div></div>
                <div class="corner bl"><div class="c1"></div><div class="c2"></div></div>
                <div class="corner br"><div class="c1"></div><div class="c2"></div></div>

                <img class="watermark" src="{{ public_path('images/logo/alu-la-solution-compact.png') }}" alt="">

                <div class="cert-number">N&deg; {{ $attestation->numero_attestation }}</div>

                <div class="header">
                    <div class="logo-frame">
                        <img src="{{ public_path('images/logo/alu-la-solution-compact.png') }}" alt="Logo">
                    </div>
                    <div class="company">
                        <div class="name">{{ config('business.name') }}</div>
                        <small>{{ config('business.activities') }}</small>
                    </div>
                    <div class="ornament-divider"><hr><div class="diamond"></div></div>
                    <div class="contact">{{ config('business.address') }} — Tél. : {{ config('business.phone') }} — {{ config('business.email') }}</div>

                    <h1>Certificat de fin d’apprentissage</h1>
                    <div class="subtitle">Décerné en reconnaissance d’une formation professionnelle accomplie avec succès</div>
                </div>

                <div class="content">
                    <table class="identity-grid">
                        <tr>
                            <td class="photo-cell">
                                @if ($photoDataUri)
                                    <div class="photo-frame has-photo">
                                        <img src="{{ $photoDataUri }}" alt="Photo de l’apprenti">
                                    </div>
                                @else
                                    <div class="photo-frame"><span>Photo de<br>l’Apprenti</span></div>
                                @endif
                                <div class="photo-caption"><strong>{{ $attestation->apprenti_nom_prenom }}</strong></div>
                            </td>
                            <td>
                                <div class="cert-text">
                                    <p>Nous, soussignés, <strong>{{ config('business.manager') }}</strong>, Maître Menuisier Alu et Vitrier, Directeur de l’établissement <strong>{{ config('business.name') }}</strong>, certifions par les présentes que :</p>
                                </div>

                                <div class="apprentice-name">{{ $attestation->apprenti_nom_prenom }}</div>

                                <div class="cert-text">
                                    <p>
                                        né(e) le <strong>{{ $attestation->date_naissance?->translatedFormat('d F Y') ?? '—' }}</strong>
                                        à <strong>{{ $attestation->lieu_naissance ?: '—' }}</strong>,
                                        de nationalité <strong>{{ $attestation->nationalite }}</strong>,
                                        a suivi avec assiduité et succès sa période d’apprentissage professionnel d’une durée de
                                        <strong>{{ $dureeLettres }} ({{ sprintf('%02d', $dureeValeur) }} {{ $dureeUnite }})</strong>,
                                        du <strong>{{ $attestation->date_debut_apprentissage->translatedFormat('d F Y') }}</strong>
                                        au <strong>{{ $attestation->date_fin_apprentissage->translatedFormat('d F Y') }}</strong>.
                                    </p>
                                </div>

                                <div class="specialty-wrap"><span class="specialty-badge">Maître Menuisier Alu et Vitrier</span></div>

                                <div class="closing">En foi de quoi, le présent <strong>CERTIFICAT</strong> lui est délivré pour servir et valoir ce que de droit.</div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="footer">
                    <div class="footer-col footer-date">Fait à Lomé, le <strong>{{ $attestation->date_delivrance->translatedFormat('d F Y') }}</strong></div>

                    <div class="footer-col footer-stamp">
                        @if (config('business.stamp_image'))
                            <div class="stamp has-stamp">
                                <img src="{{ public_path(config('business.stamp_image')) }}" alt="Cachet officiel">
                            </div>
                        @else
                            <div class="stamp">Cachet<br>officiel</div>
                        @endif
                    </div>

                    <div class="footer-col footer-signature">
                        <div class="director-title">Le Directeur</div>
                        <div class="signature-space">
                            @if (config('business.signature_image'))
                                <img class="signature-img" src="{{ public_path(config('business.signature_image')) }}" alt="Signature">
                            @endif
                        </div>
                        <div class="signature-line"></div>
                        <div class="director-name">{{ config('business.manager') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
