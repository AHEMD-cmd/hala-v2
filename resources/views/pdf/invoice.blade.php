<!DOCTYPE html>
<html lang="nl">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factuur {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 20px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #333;
            padding: 20px;
        }

        table {
            width: 100%;
            table-layout: fixed;
        }

        .header {
            width: 100%;
            margin-bottom: 30px;
        }

        .header table {
            width: 100%;
        }

        .header td {
            vertical-align: top;
        }

        .header-left {
            width: 40%;
        }

        .logo {
            width: 180px;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 18pt;
            font-weight: bold;
            color: #8B4545;
            margin-bottom: 5px;
        }

        .header-right {
            width: 15%;
            text-align: left;
            font-size: 9pt;
            line-height: 1.4;
        }

        .invoice-details {
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .invoice-details table {
            width: 100%;
            table-layout: fixed;
        }

        .invoice-details td {
            padding: 3px 5px;
            font-size: 9pt;
        }

        .invoice-details td:nth-child(odd) {
            font-weight: bold;
            width: 7%;
        }

        .invoice-details td:nth-child(even) {
            width: 15%;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            table-layout: fixed;
        }

        .items-table th {
            background-color: #f0f0f0;
            padding: 8px;
            text-align: left;
            font-size: 9pt;
            border-bottom: 1px solid #ccc;
        }

        .items-table td {
            padding: 8px;
            font-size: 9pt;
            border-bottom: 1px solid #eee;
        }

        .items-table th:last-child,
        .items-table td:last-child {
            text-align: right;
        }

        .totals {
            margin-top: 20px;
            text-align: right;
        }

        .totals-table {
            width: 300px;
            table-layout: fixed;
            margin-left: auto;
        }

        .totals-table td {
            padding: 5px 10px;
            font-size: 9pt;
        }

        .totals-table td:last-child {
            text-align: right;
            font-weight: bold;
        }

        .grand-total {
            border-top: 2px solid #333;
        }

        .grand-total td {
            font-size: 11pt;
            font-weight: bold;
            padding-top: 10px;
        }

        .footer {
            margin-top: 40px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            font-size: 8pt;
            line-height: 1.6;
            text-align: justify;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <table>
            <tr>
                <td class="header-left" style="margin-bottom: 40px;">
                    <img src="{{ 'dashboard-logo.png' }}" class="logo" alt="Herd">
                </td>
                <td class="header-right">
                    BTW nr. : NL867887540B01<br>
                    KVK: 97044032<br>
                    Verrijn Stuartlaan 42B<br>
                    2288 EM Rijswijk<br>
                    Tel : 0686141463<br>
                    IBAN : NL17 INGB 0111 2416 42
                    ORDER NR : {{ $invoice->invoice_number }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Invoice Details -->
    <!-- Invoice Details -->
    <div class="invoice-details">
        <table>
            <tr>
                <td style="font-weight: bold;">DATUM:</td>
                <td>{{ $invoice->invoice_date->format('d-m-Y') }}</td>
                <td style="font-weight: bold;">VERKOPER:</td>
                <td>{{ $invoice->user->name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">STRAAT:</td>
                <td>{{ $invoice->customer->address ?? '' }}</td>
                <td style="font-weight: bold;">KLANT NAAM:</td>
                <td>{{ $invoice->customer->name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">POST CODE:</td>
                <td>{{ $invoice->customer->post_code ?? '' }}</td>
                <td style="font-weight: bold;">TEL:</td>
                <td>{{ $invoice->customer->phone ?? '' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">STAD:</td>
                <td>{{ $invoice->customer->city ?? '' }}</td>
                <td style="font-weight: bold;">LEVERING:</td>
                <td>{{ $invoice->LEVERING ?? '' }}</td>
            </tr>
        </table>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 60px;">Nr.</th>
                <th style="width: 280px;">Omschrijving</th>
                <th style="width: 80px;">Aantal</th>
                <th style="width: 80px;">Prijs</th>
                <th style="width: 80px;">Totaal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>€ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td>€ {{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals">
        <table class="totals-table">

            <tr class="grand-total">
                <td>Totaal:</td>
                <td>€ {{ number_format($invoice->total, 2, ',', '.') }}</td>
            </tr>

            <tr>
                <td>Aanbetaling:</td>
                <td>€ {{ number_format($invoice->initial_payment, 2, ',', '.') }}</td>
            </tr>

            <tr>
                <td>Betaalwijze:</td>
                <td>{{ $invoice->payment_method }}</td>
            </tr>

            <tr>
                <td>leveringskosten:</td>
                <td>€ {{ number_format($invoice->delivery_fee, 2, ',', '.') }}</td>
            </tr>

            <tr>
                <td>Te betalen bij levering:</td>
                <td>€ {{ number_format($invoice->to_be_paid_upon_delivery, 2, ',', '.') }}</td>
            </tr>

            {{-- <tr>
                <td>Subtotaal:</td>
                <td>€
                    {{ number_format($invoice->items->sum(fn($item) => $item->quantity * $item->unit_price), 2, ',', '.') }}
                </td>
            </tr> --}}






            {{-- @if ($invoice->delivery_fee && $invoice->delivery_fee > 0)
                <tr>
                    <td>Bezorgkosten:</td>
                    <td>€ {{ number_format($invoice->delivery_fee, 2, ',', '.') }}</td>
                </tr>
            @endif --}}

        </table>
    </div>

    <!-- Footer Terms -->

    <h3>Opmerking:</h3>
    
    @if ($invoice->notes)
        <div class="footer">
            {{ $invoice->notes }}
        </div>
    @endif
    <div class="footer">
        Bij levering verzoeken wij u de gehele dag aanwezig te zijn! Hiervoor bij voorbaat dank. Wij leveren het
        product tot aan de deur. Indien de klant wenst en/of gemonteerd te hebben, dan stelt de directie zich niet
        aansprakelijk voor eventuele schade in uw huis en aan uw eigendommen het product binnenhuis bezorgt De
        betaling dient plaats te vinden voordat wij de meubels uitladen. Vervoerkosten materiaal nemen wij niet retour.
        Indien er getakeld moet worden is de klant er verantwoordelijk voor, inclusief de kosten voor een takel. Op
        deze verkoopovereenkomst zijn de Algemene Voorwaarden van de Herd van toepassing, waarvoor wij
        u verwijzen naar de achterzijde van deze verkoopovereenkomst.<br>
        <strong>Bij retour worden verzendkosten niet terugbetaald</strong>
    </div>
</body>

</html>
