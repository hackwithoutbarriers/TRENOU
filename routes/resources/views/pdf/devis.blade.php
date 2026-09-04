<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Devis {{ $devis->numero_devis }}</title>
    <style>
        @page { margin: 24px 28px 26px; }
        body { margin: 0; color: #1e293b; font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        .page { width: 100%; }
        .top-rule { height: 5px; background: #1e293b; margin-bottom: 18px; }
        .header { width: 100%; border-bottom: 1px solid #cbd5e1; padding-bottom: 16px; }
        .header td { vertical-align: top; }
        .brand-mark { display: inline-block; background: #1e293b; color: #fff; padding: 8px 10px; font-weight: bold; font-size: 15px; letter-spacing: 1px; }
        .brand-name { margin: 7px 0 0; color: #334155; font-size: 15px; font-weight: bold; }
        .contact { margin-top: 6px; color: #64748b; line-height: 1.55; }
        .document { text-align: right; }
        .document h1 { margin: 0; color: #1e293b; font-size: 26px; letter-spacing: 2px; }
        .document .number { margin-top: 7px; color: #334155; font-size: 12px; font-weight: bold; }
        .document .date { margin-top: 4px; color: #64748b; }
        .section-label { margin: 20px 0 8px; color: #334155; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .client-box { width: 100%; border-collapse: collapse; background: #f8fafc; border: 1px solid #cbd5e1; }
        .client-box td { padding: 9px 11px; border-right: 1px solid #e2e8f0; }
        .client-box td:last-child { border-right: 0; }
        .client-label { display: block; margin-bottom: 3px; color: #64748b; font-size: 9px; text-transform: uppercase; letter-spacing: .5px; }
        .client-value { color: #1e293b; font-weight: bold; }
        .description { padding: 11px 13px; border-left: 3px solid #334155; background: #f8fafc; line-height: 1.6; }
        .pricing { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .pricing th { padding: 10px 12px; background: #334155; color: #fff; font-size: 10px; text-align: left; text-transform: uppercase; letter-spacing: .7px; }
        .pricing th.amount, .pricing td.amount { text-align: right; }
        .pricing td { padding: 12px; border: 1px solid #e2e8f0; border-top: 0; }
        .pricing tr:nth-child(even) td { background: #f8fafc; }
        .pricing td.amount { color: #1e293b; font-weight: bold; }
        .summary { width: 100%; margin-top: 16px; border-collapse: collapse; }
        .summary td { padding: 6px 12px; }
        .summary .label { color: #64748b; text-align: right; }
        .summary .value { width: 28%; text-align: right; font-weight: bold; }
        .summary .net td { padding-top: 12px; padding-bottom: 12px; border-top: 2px solid #1e293b; color: #1e293b; font-size: 16px; }
        .deposit { margin-top: 14px; padding: 12px 15px; border: 1px solid #94a3b8; background: #eef2f5; }
        .deposit strong { color: #1e293b; font-size: 13px; }
        .deposit span { float: right; color: #334155; font-weight: bold; font-size: 13px; }
        .closing { width: 100%; margin-top: 22px; border-top: 1px solid #cbd5e1; padding-top: 12px; }
        .closing td { width: 50%; vertical-align: top; color: #64748b; font-size: 10px; line-height: 1.55; }
        .signature { padding-top: 29px; color: #334155; font-weight: bold; }
        .signature-line { width: 75%; border-top: 1px solid #64748b; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="page">
        <div class="top-rule"></div>
        <table class="header">
            <tr>
                <td style="width: 62%;">
                    <div class="brand-mark">ARA TECH</div>
                    <div class="brand-name">Artisan Menuisier d’Aluminium</div>
                    <div class="contact">Lomé, Togo<br>Tél. : +228 90 12 34 56<br>WhatsApp : +228 90 12 34 56</div>
                </td>
                <td class="document">
                    <h1>DEVIS</h1>
                    <div class="number">{{ $devis->numero_devis }}</div>
                    <div class="date">Émis le {{ $devis->created_at->translatedFormat('d F Y') }}</div>
                </td>
            </tr>
        </table>

        <div class="section-label">Informations du client</div>
        <table class="client-box">
            <tr>
                <td style="width: 42%;"><span class="client-label">Client</span><span class="client-value">{{ $devis->client_nom }}</span></td>
                <td style="width: 28%;"><span class="client-label">Téléphone</span><span class="client-value">{{ $devis->client_telephone }}</span></td>
                <td><span class="client-label">Localisation</span><span class="client-value">{{ $devis->client_ville ?? 'Non renseignée' }}, {{ $devis->client_pays }}</span></td>
            </tr>
        </table>

        <div class="section-label">Objet de la prestation</div>
        <div class="description">{{ $devis->description_chantier }}</div>

        <table class="pricing">
            <thead><tr><th>Désignation</th><th class="amount">Montant (FCFA)</th></tr></thead>
            <tbody>
                <tr><td>Sous-total matériel</td><td class="amount">{{ number_format((float) $devis->montant_materiel, 0, ',', ' ') }}</td></tr>
                <tr><td>Sous-total main-d’œuvre</td><td class="amount">{{ number_format((float) $devis->montant_main_doeuvre, 0, ',', ' ') }}</td></tr>
            </tbody>
        </table>

        <table class="summary">
            <tr><td class="label">Sous-total matériel</td><td class="value">{{ number_format((float) $devis->montant_materiel, 0, ',', ' ') }} FCFA</td></tr>
            <tr><td class="label">Sous-total main-d’œuvre</td><td class="value">{{ number_format((float) $devis->montant_main_doeuvre, 0, ',', ' ') }} FCFA</td></tr>
            <tr class="net"><td class="label">MONTANT TOTAL NET</td><td class="value">{{ number_format((float) $devis->montant_total, 0, ',', ' ') }} FCFA</td></tr>
        </table>

        <div class="deposit"><strong>Acompte requis au démarrage</strong><span>{{ (int) $devis->acompte_requis_pourcentage }} % du montant total</span></div>

        <table class="closing">
            <tr>
                <td>Validité du devis : 30 jours à compter de sa date d’émission.<br>Référence séquentielle : <strong>{{ $devis->numero_devis }}</strong></td>
                <td class="signature"><div class="signature-line">Accord client / validation</div></td>
            </tr>
        </table>
    </div>
</body>
</html>
