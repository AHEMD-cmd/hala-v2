<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8fafc;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .invoice-details {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #2563eb;
        }
        .invoice-details p {
            margin: 8px 0;
        }
        .invoice-details strong {
            color: #2563eb;
        }
        .footer {
            background-color: #1e40af;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 5px 5px;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .attachment-note {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Hala Wonen</h1>
        <p>Bedankt voor uw aankoop!</p>
    </div>

    <div class="content">
        <h2>Beste {{ $invoice->customer->name }},</h2>
        
        <p>Hartelijk dank voor uw recente aankoop bij Hala Wonen. In de bijlage vindt u uw factuur.</p>

        <div class="invoice-details">
            <p><strong>Factuurnummer:</strong> {{ $invoice->invoice_number }}</p>
            <p><strong>Factuurdatum:</strong> {{ $invoice->invoice_date->format('d-m-Y') }}</p>
            <p><strong>Totaalbedrag:</strong> € {{ number_format($invoice->total, 2, ',', '.') }}</p>
            <p><strong>Verkoper:</strong> {{ $invoice->user->name }}</p>
        </div>

        <div class="attachment-note">
            <strong>📎 Bijlage:</strong> Uw factuur is als PDF-bestand bijgevoegd aan deze e-mail.
        </div>

        <p>Als u vragen heeft over deze factuur, neem dan gerust contact met ons op.</p>

        <p>Met vriendelijke groet,<br>
        <strong>Het team van Hala Wonen</strong></p>
    </div>

    <div class="footer">
        <p><strong>Hala Wonen</strong></p>
        <p>Verrijn Stuartlaan 42 B, 2288 EM Rijswijk, Nederland</p>
        <p>Tel: +31 68 614 1463 | Email: info@halawonen.nl</p>
        <p>Website: <a href="https://halawonen.nl" style="color: white;">halawonen.nl</a></p>
    </div>
</body>
</html>