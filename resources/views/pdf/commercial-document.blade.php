<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 14mm 14mm 18mm }
        * { box-sizing: border-box }
        body { margin: 0; color: #172033; font-family: DejaVu Sans, sans-serif; font-size: 9px }
        .page-border { position: fixed; z-index: -1; top: -7mm; right: -7mm; bottom: -11mm; left: -7mm; border: 1.2px dashed #c7192c; border-radius: 4mm }
        .header { padding: 1mm 2mm 6mm; border-bottom: 1.5px solid #c7192c; margin-bottom: 7mm }
        .header-table, .info, .totals { width: 100%; border-collapse: collapse }
        .logo { width: 65mm; max-height: 25mm; object-fit: contain }
        .brand { text-align: right }
        .brand h1 { margin: 0; color: #c7192c; font-size: 23px; letter-spacing: .5px }
        .brand p { margin: 4px 0 0; color: #64748b; font-size: 8px; letter-spacing: 1.5px }
        .info { margin-bottom: 8mm }
        .info td { width: 50%; padding: 5mm; border: 1px solid #e5e7eb; vertical-align: top; background: #fff }
        .info td:first-child { border-left: 3px solid #c7192c }
        .label { display: block; margin-bottom: 3px; color: #94a3b8; font-size: 7px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px }
        .recipient { font-size: 13px; font-weight: bold }
        .contact { margin-top: 4px; color: #64748b; line-height: 1.6 }
        .meta { text-align: right; line-height: 1.8 }
        .meta strong { color: #c7192c }
        .section-title { margin: 0 0 3mm; padding-left: 2mm; border-left: 2px solid #c7192c; font-size: 10px; font-weight: bold }
        .lines { width: 100%; border-collapse: collapse }
        .lines th { padding: 3mm 2mm; background: #172033; color: #fff; text-align: left; font-size: 7px; text-transform: uppercase; letter-spacing: .5px }
        .lines th:first-child { border-radius: 2mm 0 0 0 }
        .lines th:last-child { border-radius: 0 2mm 0 0 }
        .lines td { padding: 3.2mm 2mm; border-bottom: 1px solid #e5e7eb; vertical-align: top }
        .lines tbody tr:nth-child(even) { background: #f8fafc }
        .money { text-align: right !important; white-space: nowrap; font-weight: bold }
        .route { margin-top: 3px; color: #64748b; font-size: 8px }
        .totals { width: 82mm; margin: 8mm 0 0 auto }
        .totals td { padding: 2.7mm 3mm; border-bottom: 1px solid #e5e7eb }
        .totals td:last-child { text-align: right; font-weight: bold; white-space: nowrap }
        .totals .grand td { padding-top: 4mm; border-top: 2px solid #c7192c; border-bottom: 0; color: #c7192c; font-size: 13px }
        .notice { margin-top: 8mm; padding: 4mm; border: 1px dashed #efb0b8; border-radius: 3mm; background: #fff8f8; color: #64748b; line-height: 1.6 }
        .footer { position: fixed; bottom: -9mm; left: 0; right: 0; color: #94a3b8; font-size: 7px; text-align: center }
        .accent { color: #c7192c }
    </style>
</head>
<body>
    <div class="page-border"></div>
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    @if ($logoDataUri)
                        <img class="logo" src="{{ $logoDataUri }}" alt="GORAKMANA">
                    @endif
                </td>
                <td class="brand">
                    <h1>{{ $documentTitle }}</h1>
                    <p>SOLUTIONS DIGITALES</p>
                </td>
            </tr>
        </table>
    </div>
    <table class="info">
        <tr>
            <td>
                <span class="label">{{ $documentTitle === 'FACTURE' ? 'Facturé à' : ($documentTitle === 'COMMANDE' ? 'Commande de' : 'Proposé à') }}</span>
                <div class="recipient">{{ $recipient->name }}</div>
                <div class="contact">
                    @if (!empty($recipient->address))
                        {{ $recipient->address }}<br>
                    @endif
                    @if (!empty($recipient->email))
                        {{ $recipient->email }}
                    @endif
                    @if (!empty($recipient->email) && !empty($recipient->phone))
                        ·
                    @endif
                    @if (!empty($recipient->phone))
                        {{ $recipient->phone }}
                    @endif
                    @if (empty($recipient->address) && empty($recipient->email) && empty($recipient->phone))
                        Coordonnées non renseignées
                    @endif
                </div>
            </td>
            <td class="meta">
                <span class="label">Informations {{ strtolower($documentTitle) }}</span>
                <div><strong>N° {{ $documentNumber }}</strong></div>
                <div>Date : {{ $documentDate->format('d/m/Y') }}</div>
                @if ($dueDate)
                    <div>Échéance : {{ $dueDate->format('d/m/Y') }}</div>
                @endif
                <div>Devise : MAD</div>
            </td>
        </tr>
    </table>
    <div class="section-title">DÉTAIL DES PRESTATIONS</div>
    <table class="lines">
        <thead>
            <tr>
                <th>Date</th>
                <th>Référence</th>
                <th>Service / Désignation</th>
                <th>Qté</th>
                <th class="money">Prix HT</th>
                <th class="money">Montant TTC</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row['date'] }}</td>
                    <td><strong>{{ $row['reference'] }}</strong></td>
                    <td>
                        <strong>{{ $row['service'] }}</strong>
                        <div class="route">{{ $row['route'] }}</div>
                    </td>
                    <td>{{ number_format((float) $row['quantity'], 2, ',', ' ') }}</td>
                    <td class="money">{{ number_format((float) $row['unit_price_ht'], 2, ',', ' ') }} MAD</td>
                    <td class="money">{{ number_format((float) $row['amount_ttc'], 2, ',', ' ') }} MAD</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <table class="totals">
        <tr>
            <td>Total HT</td>
            <td>{{ number_format((float) $summary['total_ht'], 2, ',', ' ') }} MAD</td>
        </tr>
        <tr>
            <td>TVA</td>
            <td>{{ number_format((float) $summary['vat_amount'], 2, ',', ' ') }} MAD</td>
        </tr>
        <tr class="grand">
            <td>Total TTC</td>
            <td>{{ number_format((float) $summary['total_ttc'], 2, ',', ' ') }} MAD</td>
        </tr>
    </table>
    <div class="notice">{{ $notice }}</div>
    <div class="footer">GORAKMANA · Solutions digitales & technologiques · Document généré le {{ $generatedAt->format('d/m/Y à H:i') }}</div>
</body>
</html>
