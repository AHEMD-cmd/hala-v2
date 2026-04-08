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
            line-height: 1.6;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
        }
        
        .header-left {
            width: 50%;
        }
        
        .header-right {
            width: 50%;
            text-align: right;
        }
        
        .company-name {
            font-size: 24pt;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .company-details {
            font-size: 9pt;
            color: #666;
            line-height: 1.4;
        }
        
        .invoice-title {
            font-size: 28pt;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        
        .invoice-meta {
            font-size: 10pt;
        }
        
        .invoice-meta-row {
            margin-bottom: 5px;
        }
        
        .invoice-meta-label {
            display: inline-block;
            width: 120px;
            font-weight: bold;
        }
        
        .billing-info {
            display: flex;
            margin: 30px 0;
        }
        
        .billing-to {
            width: 50%;
            padding: 20px;
            background-color: #f8fafc;
            border-radius: 5px;
        }
        
        .billing-label {
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 10px;
            color: #2563eb;
        }
        
        .customer-details {
            font-size: 10pt;
            line-height: 1.6;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        
        .items-table thead {
            background-color: #2563eb;
            color: white;
        }
        
        .items-table th {
            padding: 12px 10px;
            text-align: left;
            font-weight: bold;
            font-size: 10pt;
        }
        
        .items-table th.right {
            text-align: right;
        }
        
        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10pt;
        }
        
        .items-table td.right {
            text-align: right;
        }
        
        .totals {
            margin-top: 20px;
            text-align: right;
        }
        
        .totals-table {
            display: inline-block;
            min-width: 300px;
        }
        
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }
        
        .totals-label {
            font-size: 11pt;
        }
        
        .totals-value {
            font-size: 11pt;
            font-weight: bold;
        }
        
        .grand-total {
            border-top: 2px solid #2563eb;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .grand-total .totals-label,
        .grand-total .totals-value {
            font-size: 14pt;
            color: #2563eb;
        }
        
        .notes {
            margin-top: 40px;
            padding: 20px;
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 5px;
        }
        
        .notes-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 8px;
            color: #92400e;
        }
        
        .notes-content {
            font-size: 10pt;
            color: #78350f;
            line-height: 1.5;
        }
        
        .footer {
            margin-top: 60px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }
        
        .seller-info {
            margin-top: 20px;
            font-size: 10pt;
            color: #666;
        }

        /* Print-specific styles */
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            @page {
                margin: 1cm;
            }
        }

        /* Print button styles */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        .print-button:hover {
            background-color: #1d4ed8;
        }

        .print-button:active {
            background-color: #1e40af;
        }
    </style>
</head>
<body>
    <!-- Print Button -->
    <button onclick="window.print()" class="print-button no-print">
        🖨️ Print Invoice
    </button>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <div class="company-name">Hala Wonen</div>
                <div class="company-details">
                    Verrijn Stuartlaan 42 B<br>
                    2288 EM Rijswijk<br>
                    Nederland<br>
                    Tel: +31 68 614 1463<br>
                    Email: info@herd.com
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-title">FACTUUR</div>
                <div class="invoice-meta">
                    <div class="invoice-meta-row">
                        <span class="invoice-meta-label">Factuurnummer:</span>
                        <span>{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="invoice-meta-row">
                        <span class="invoice-meta-label">Factuurdatum:</span>
                        <span>{{ $invoice->invoice_date->format('d-m-Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="billing-info">
            <div class="billing-to">
                <div class="billing-label">Factureren aan:</div>
                <div class="customer-details">
                    <strong>{{ $invoice->customer->name }}</strong><br>
                    @if($invoice->customer->email)
                        {{ $invoice->customer->email }}<br>
                    @endif
                    @if($invoice->customer->phone)
                        {{ $invoice->customer->phone }}<br>
                    @endif
                    @if($invoice->customer->address)
                        {{ $invoice->customer->address }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 10%;">#</th>
                    <th style="width: 45%;">Productbeschrijving</th>
                    <th style="width: 15%;" class="right">Aantal</th>
                    <th style="width: 15%;" class="right">Prijs</th>
                    <th style="width: 15%;" class="right">Totaal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
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

        <!-- Totals -->
        <div class="totals">
            <div class="totals-table">
                <div class="totals-row">
                    <div class="totals-label">Subtotaal:</div>
                    <div class="totals-value">€ {{ number_format($invoice->items->sum(fn($item) => $item->quantity * $item->unit_price), 2, ',', '.') }}</div>
                </div>
                
                @if($invoice->delivery_fee && $invoice->delivery_fee > 0)
                <div class="totals-row">
                    <div class="totals-label">Bezorgkosten:</div>
                    <div class="totals-value">€ {{ number_format($invoice->delivery_fee, 2, ',', '.') }}</div>
                </div>
                @endif
                
                <div class="totals-row grand-total">
                    <div class="totals-label">Totaalbedrag:</div>
                    <div class="totals-value">€ {{ number_format($invoice->total, 2, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($invoice->notes)
        <div class="notes">
            <div class="notes-title">Opmerkingen:</div>
            <div class="notes-content">{{ $invoice->notes }}</div>
        </div>
        @endif

        <!-- Seller Info -->
        <div class="seller-info">
            <strong>Verkoper:</strong> {{ $invoice->user->name }}
        </div>

        <!-- Footer -->
        <div class="footer">
            Bedankt voor uw aankoop!<br>
            Voor vragen kunt u contact met ons opnemen via info@herd.com of +31 68 614 1463
        </div>
    </div>

    <script>
        // Auto-open print dialog when page loads (optional)
        // window.onload = function() {
        //     window.print();
        // };
    </script>
</body>
</html>