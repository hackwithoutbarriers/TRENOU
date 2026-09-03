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
        body { color: #16305c; font-family: 'Roboto', Arial, sans-serif; font-size: 14pt; background: #c9c9c9; }

        /* ===== Cadre extérieur ===== */
        .certificate {
            position: relative;
            width: 297mm;
            height: 210mm;
            padding: 6mm;
            background: #f7f7f5;
        }

        .border-frame {
            position: relative;
            height: 100%;
            border: 5px double #16305c;
            padding: 4mm;
        }

        .border-inner {
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
            border: 1px solid #8b96a3;
            padding: 5mm 12mm 6mm;
        }

        /* ===== Coins décoratifs (sans SVG, compatible dompdf) ===== */
        .corner { position: absolute; width: 34px; height: 34px; z-index: 2; }
        .corner .c1 { position:absolute; top:0; left:0; width:100%; height:100%; border-top:4px solid #16305c; border-left:4px solid #16305c; }
        .corner .c2 { position:absolute; top:7px; left:7px; width:100%; height:100%; border-top:2px solid #b9c2cc; border-left:2px solid #b9c2cc; }
        .corner.tl { top: -5px; left: -5px; }
        .corner.tr { top: -5px; right: -5px; transform: scaleX(-1); }
        .corner.bl { bottom: -5px; left: -5px; transform: scaleY(-1); }
        .corner.br { bottom: -5px; right: -5px; transform: scale(-1,-1); }

        .watermark { position: absolute; top: 50%; left: 50%; width: 82mm; opacity: .07; transform: translate(-50%, -50%); z-index: 0; }

        /* ===== En-tête ===== */
        .header { position: relative; z-index: 1; text-align: center; }
        .logo-frame { width: 92px; height: 64px; margin: 0 auto 2mm; text-align: center; line-height: 64px; }
        .logo-frame img { width: auto; height: auto; max-width: 92px; max-height: 64px; object-fit: contain; vertical-align: middle; }
        .company { text-align: center; }
        .company .name { font-family: 'Helvetica', Arial, sans-serif; font-size: 18pt; font-weight: bold; color: #16305c; letter-spacing: .5px; line-height: 1.1; }
        .company small { display: block; font-size: 11pt; font-weight: normal; color: #222; margin-top: 2mm; line-height: 1.25; }

        .sep { border: none; border-top: 1.5px solid #16305c; margin: 3mm 0 2mm; }
        .contact { font-size: 14pt; color: #111; line-height: 1.2; }

        h1 { margin: 4mm 0 2mm; font-family: 'Helvetica', Arial, sans-serif; font-size: 24pt; letter-spacing: 1px; color: #111; }
        .subtitle { font-size: 12pt; font-weight: bold; color: #333; }
        .number { margin: 3mm auto 0; width: 48mm; padding: 2mm; border: 1px solid #16305c; font-size: 11pt; }

        /* ===== Corps ===== */
        .content { position: absolute; z-index: 1; top: 54mm; right: 0; bottom: 32mm; left: 0; display: flex; align-items: center; justify-content: center; margin: 0; line-height: 1.55; }

        .identity-grid { width: 100%; margin: 0 auto; border-collapse: collapse; }
        .identity-grid > tbody > tr > td { padding: 4mm 6mm; vertical-align: middle; }

        .photo-cell { width: 23%; text-align: center; }
        .photo-frame {
            display: flex; width: 35mm; height: 35mm; margin: 0 auto 3mm; align-items: center; justify-content: center;
            border: 2px dashed #8b96a3; text-align: center; font-size: 10pt; color: #8a8a8a;
        }
        .photo-frame.has-photo { border: 1px solid #16305c; }
        .photo-frame img { display: block; width: 100%; height: 100%; object-fit: contain; background: #f1f3f5; }
        .photo-frame span { display: block; }
        .photo-caption { font-size: 10pt; }
        .photo-caption strong { display: block; margin-top: 2px; }

        .identity-grid td {
            font-size: 10.5pt;
            line-height: 1.8;
        }
        .identity-grid td > div + div {
            margin-top: 2.5mm;
        }
        .lead { margin: 2mm 0 1.5mm; font-size: 15pt; font-weight: bold; text-transform: uppercase; }
        .award { margin: 2mm 0; text-align: left; font-size: 12pt; font-weight: bold; }

        .details { width: 100%; margin: 3mm 0 0; border-collapse: collapse; }
        .details td { padding: 1.5mm 0; border-bottom: 1px solid #b9c8d5; font-size: 10pt; }
        .details .label { width: 22%; color: #333; }
        .details .value { font-weight: bold; }

        /* ===== Pied de page ===== */
        .footer { position: absolute; z-index: 1; right: 0; bottom: 0; left: 0; padding-top: 4mm; border-top: 1px solid #16305c; }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { vertical-align: bottom; font-size: 11pt; }

        .date-cell {
            position: absolute;
            right: 0;
            bottom: 5mm;
            left: 0;
            z-index: 2;
            width: 100%;
            text-align: center;
            vertical-align: middle;
            transform: translateY(-3mm);
        }

        .stamp-cell {
            width: 22%;
            padding-bottom: 2mm;
            padding-right: 2mm;
            text-align: right;
            vertical-align: middle;
            transform: translateX(22mm);
        }
        .stamp {
            display: inline-block; width: 25mm; height: 25mm; line-height: 25mm;
            text-align: center; font-size: 9pt; color: #8a8a8a;
        }
        .stamp.has-stamp { line-height: normal; }
        .stamp img { display: block; width: 100%; height: 100%; max-width: 100%; max-height: 100%; object-fit: contain; vertical-align: middle; }

        .director-cell { width: 37%; text-align: center; }
        .director-title { font-weight: bold; margin-bottom: 2mm; }
        .signature-space { height: 12mm; }
        .signature-img { display: block; width: 28mm; height: 10mm; margin: 0 auto; object-fit: contain; }
        .signature-line { width: 34mm; height: 1px; margin: 0 auto; border-bottom: 1px solid #16305c; }
        .director-name { font-weight: bold; margin-top: -3mm; transform: translateY(-2mm); }
        .director-meta { font-size: 9pt; color: #333; margin-top: 2mm; line-height: 1.4; }

    </style>
</head>
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
        <div class="border-frame">
            <div class="corner tl"><div class="c1"></div><div class="c2"></div></div>
            <div class="corner tr"><div class="c1"></div><div class="c2"></div></div>
            <div class="corner bl"><div class="c1"></div><div class="c2"></div></div>
            <div class="corner br"><div class="c1"></div><div class="c2"></div></div>

            <div class="border-inner">
                <img class="watermark" src="{{ public_path('images/logo/alu-la-solution-compact.png') }}" alt="">

                <div class="header">
                    <div class="logo-frame">
                        <img src="{{ public_path('images/logo/alu-la-solution-compact.png') }}" alt="Logo">
                    </div>
                    <div class="company">
                        <div class="name">{{ config('business.name') }}</div>
                        <small>{{ config('business.activities') }}</small>
                    </div>
                    <hr class="sep">
                    <div class="contact">{{ config('business.address') }} — Tél. : {{ config('business.phone') }} — {{ config('business.email') }}</div>
                    <h1>CERTIFICAT DE FIN D’APPRENTISSAGE</h1>
                </div>

                <div class="content">
                    <table class="identity-grid">
                        <tr>
                            <td class="photo-cell">
                                @if ($attestation->photo_profil)
                                    <div class="photo-frame has-photo">
                                        <img src="{{ public_path('storage/'.$attestation->photo_profil) }}" alt="Photo de l’apprenti">
                                    </div>
                                @else
                                    <div class="photo-frame"><span>Photo de l’Apprenti</span></div>
                                @endif
                                <div class="photo-caption">
                                    <strong>{{ $attestation->apprenti_nom_prenom }}</strong>
                                </div>
                            </td>
                            <td>
                                <div>Nous, soussignés, <strong>{{ config('business.manager') }}</strong>, <span>Maître Menuisier Alu et
                                    Vitrier</span>, Directeur de l’Etablissement <br><span class="award">{{ config('business.name') }}</span> certifie par la présente
                                    que le nommé <strong class="lead">{{ $attestation->apprenti_nom_prenom }}</strong>
                                </div>
                                
                                <div>
                                    né(e) le <strong>{{ $attestation->date_naissance?->translatedFormat('d F Y') ?? '—' }}</strong>
                                    à <strong>{{ $attestation->lieu_naissance ?: '—' }}</strong>,
                                    de nationalité <strong>{{ $attestation->nationalite }}</strong>,
                                    a accompli avec assiduité sa période d’apprentissage professionnel pendant une durée de
                                    <strong>{{ $dureeLettres }} ({{ sprintf('%02d', $dureeValeur) }} {{ $dureeUnite }})</strong>
                                    du <strong>{{ $attestation->date_debut_apprentissage->translatedFormat('d F Y') }}</strong>
                                    au <strong>{{ $attestation->date_fin_apprentissage->translatedFormat('d F Y') }}</strong>.
                                </div>
                                <div>Dès aujourd'hui et pour toujours, nous lui donnons le titre d’un compétent <span class="award">Maître Menuisier Alu et Vitrier</span></div>           
                                <div>En foi de qui, nous lui délivrons ce <strong>CERTIFICAT</strong> pour servir et valoir ce que de droit.</div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="footer">
                    <table class="footer-table">
                        <tr>
                            <td class="date-cell">Fait à Lomé, le <strong>{{ $attestation->date_delivrance->translatedFormat('d F Y') }}</strong></td>
                            <td class="stamp-cell">
                                @if (config('business.stamp_image'))
                                    <div class="stamp has-stamp">
                                        <img src="{{ public_path(config('business.stamp_image')) }}" alt="Cachet officiel">
                                    </div>
                                @else
                                    <div class="stamp">Cachet officiel</div>
                                @endif
                            </td>
                            <td class="director-cell">
                                <div class="director-title">Le Directeur</div>
                                <div class="signature-space">
                                    @if (config('business.signature_image'))
                                        <img class="signature-img" src="{{ public_path(config('business.signature_image')) }}" alt="Signature">
                                    @endif
                                </div>
                                <div class="signature-line"></div>
                                <div class="director-name"><span class="award">{{ config('business.manager') }}</span></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>