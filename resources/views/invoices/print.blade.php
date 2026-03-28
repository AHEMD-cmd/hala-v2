<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Invoice {{ $invoice->invoice_number }}</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            color: #333;
            padding: 40px 20px 20px 20px;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background-color: #8B4545;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .print-button:hover {
            background-color: #6d3535;
        }

        @media print {

            .print-button {
                display: none !important;
            }

            body {
                padding: 0;
            }

            @page {
                margin: 0;
            }



            .label {
                color: transparent;
            }

            .order-number-label {
                color: #333 !important;
            }

        }

        /* Invoice Details */

        .invoice-details {
            margin-bottom: 30px;
            margin-top: 331px;
        }

        .invoice-details table {
            width: 100%;
            table-layout: fixed;
        }

        .invoice-details td {
            padding: 5px 10px;
            font-size: 10pt;
            vertical-align: top;
        }

        .invoice-details td:nth-child(odd) {
            font-weight: bold;
            width: 20%;
        }

        .invoice-details td:nth-child(even) {
            width: 30%;
        }

        /* Items Table */

        .items-table {
            width: 90%;
            border-collapse: collapse;
            margin: 20px auto;
            table-layout: fixed;
        }

        .items-table th {
            background-color: #f0f0f0;
            padding: 10px 8px;
            text-align: left;
            font-size: 10pt;
            border-bottom: 1px solid #ccc;
        }

        .items-table td {
            padding: 10px 8px;
            font-size: 10pt;
            border-bottom: 1px solid #eee;
        }

        .items-table th:last-child,
        .items-table td:last-child {
            text-align: right;
        }

        .items-table th.right,
        .items-table td.right {
            text-align: right;
        }

        /* Totals */

        .totals {
            margin-top: 20px;
            margin-right: 45px;
            text-align: right;
        }

        .totals-table {
            width: 300px;
            table-layout: fixed;
            margin-left: auto;
        }

        .totals-table td {
            padding: 5px 10px;
            font-size: 10pt;
        }

        .totals-table td:last-child {
            text-align: right;
            font-weight: bold;
        }

        .grand-total {
            border-top: 2px solid #333;
        }

        .grand-total td {
            font-size: 12pt;
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


        .notes-section {
            width: 90%;
            margin: 25px auto;
        }

        .notes-title {
            font-weight: bold;
            margin-bottom: 8px;
        }

        .notes-box {
            height: 80px;
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #f9f9f9;  
        }
    </style>
</head>

<body>

    <button onclick="window.print()" class="print-button">
        🖨️ Print Invoice
    </button>

    <div class="invoice-details">

        <table>

            <tr>
                <td class="label">DATUM:</td>
                <td>{{ $invoice->invoice_date->format('d-m-Y') }}</td>

                <td class="label">VERKOPER:</td>
                <td>{{ $invoice->user->name }}</td>
            </tr>

            <tr>
                <td class="label">STRAAT:</td>
                <td>{{ $invoice->customer->address ?? '' }}</td>

                <td class="label">KLANT NAAM:</td>
                <td>{{ $invoice->customer->name }}</td>
            </tr>

            <tr>
                <td class="label">POST CODE:</td>
                <td>{{ $invoice->customer->post_code ?? '' }}</td>

                <td class="label">TEL:</td>
                <td>{{ $invoice->customer->phone ?? '' }}</td>
            </tr>

            <tr>
                <td class="label">STAD:</td>
                <td>{{ $invoice->customer->city ?? '' }}</td>

                <td class="label">LEVERING:</td>
                <td>{{ $invoice->LEVERING ?? '' }}</td>
            </tr>

            <tr>
                <td></td>
                <td></td>

                <!--<td class="label">ORDER NUMBER:</td>-->
                <td class="order-number-label">ORDER NUMBER:</td>
                <td>{{ $invoice->invoice_number ?? '' }}</td>
            </tr>

        </table>

    </div>


    <table class="items-table">

        <thead>

            <tr>
                <th style="width:60px;">Nr.</th>
                <th style="width:280px;">Omschrijving</th>
                <th style="width:80px;" class="right">Aantal</th>
                <th style="width:80px;" class="right">Prijs</th>
                <th style="width:80px;" class="right">Totaal</th>
            </tr>

        </thead>

        <tbody>

            @foreach ($invoice->items as $index => $item)
                <tr>

                    <td>{{ $index + 1 }}</td>

                    <td>{{ $item->product->name }}</td>

                    <td class="right">{{ $item->quantity }}</td>

                    <td class="right">€ {{ number_format($item->unit_price, 2, ',', '.') }}</td>

                    <td class="right">€ {{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }}</td>

                </tr>
            @endforeach

        </tbody>

    </table>


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

    <div class="notes-section">

        <div class="notes-title">Opmerking:</div>

        <div class="notes-box">
            {{ $invoice->notes }}
        </div>

    </div>
</body>

</html>
