<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Attestation {{ $attestation->numero_attestation }}</title>
    <style>
        @page { margin: 22px; }
        body { margin: 0; color: #1e293b; font-family: DejaVu Sans, sans-serif; }
        .certificate { min-height: 100%; padding: 8px; border: 2px solid #334155; background: #fff; }
        .inner { min-height: 490px; padding: 28px 48px 22px; border: 1px solid #94a3b8; position: relative; }
        .watermark { position: absolute; top: 185px; left: 260px; color: #f1f5f9; font-size: 62px; font-weight: bold; letter-spacing: 8px; }
        .header { position: relative; text-align: center; border-bottom: 1px solid #cbd5e1; padding-bottom: 17px; }
        .atelier { color: #64748b; font-size: 10px; text-transform: uppercase; letter-spacing: 3px; }
        h1 { margin: 12px 0 6px; color: #1e293b; font-family: Georgia, serif; font-size: 30px; letter-spacing: 2px; }
        .subtitle { color: #475569; font-size: 12px; text-transform: uppercase; letter-spacing: 2px; }
        .number { display: inline-block; margin-top: 12px; padding: 6px 18px; border: 1px solid #64748b; color: #334155; font-size: 11px; font-weight: bold; letter-spacing: 1px; }
        .content { position: relative; margin: 30px auto 0; max-width: 750px; text-align: center; font-size: 13px; line-height: 1.7; }
        .lead { margin: 13px 0 10px; color: #1e293b; font-family: Georgia, serif; font-size: 27px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .specialisations { margin: 15px auto; width: 70%; color: #334155; font-size: 14px; font-weight: bold; }
        .details { position: relative; width: 72%; margin: 18px auto 0; border-collapse: collapse; }
        .details td { padding: 7px 12px; border-bottom: 1px solid #e2e8f0; font-size: 11px; text-align: left; }
        .details .label { width: 42%; color: #64748b; text-transform: uppercase; letter-spacing: .5px; }
        .details .value { color: #1e293b; font-weight: bold; }
        .footer { position: relative; width: 100%; margin-top: 26px; border-top: 1px solid #cbd5e1; padding-top: 14px; }
        .footer td { width: 33%; vertical-align: top; color: #475569; font-size: 10px; text-align: center; }
        .footer strong { display: block; margin-bottom: 30px; color: #1e293b; font-size: 11px; }
        .stamp { height: 58px; margin: 0 auto; border: 1px dashed #94a3b8; color: #64748b; line-height: 58px; }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="inner">
            <div class="watermark">ARA</div>
            <div class="header">
                <div class="atelier">ARA Tech · Atelier de Menuiserie d’Aluminium · Lomé, Togo</div>
                <h1>Attestation de formation</h1>
                <div class="subtitle">Certificat officiel de fin d’apprentissage</div>
                <div class="number">N° {{ $attestation->numero_attestation }}</div>
            </div>
            <div class="content">
                <div>La direction de l’atelier certifie que</div>
                <div class="lead">{{ $attestation->apprenti_nom_prenom }}</div>
                <div>a suivi avec assiduité et a validé le cycle de formation professionnelle<br>dans les spécialisations suivantes :</div>
                <div class="specialisations">
                    @foreach (preg_split('/\s*,\s*/', $attestation->specialisations) as $specialisation)
                        <div>• {{ $specialisation }}</div>
                    @endforeach
                </div>
                <div>Formation suivie du <strong>{{ $attestation->date_debut_apprentissage->translatedFormat('d F Y') }}</strong> au <strong>{{ $attestation->date_fin_apprentissage->translatedFormat('d F Y') }}</strong>.</div>
            </div>
            <table class="details">
                <tr><td class="label">Numéro unique</td><td class="value">{{ $attestation->numero_attestation }}</td></tr>
                <tr><td class="label">Date de délivrance</td><td class="value">{{ $attestation->date_delivrance->translatedFormat('d F Y') }}</td></tr>
            </table>
            <table class="footer">
                <tr>
                    <td><strong>Fait à Lomé, le</strong>{{ $attestation->date_delivrance->translatedFormat('d F Y') }}</td>
                    <td><strong>Signature de la direction</strong></td>
                    <td><strong>Cachet officiel de l’atelier</strong><div class="stamp">Cachet</div></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
