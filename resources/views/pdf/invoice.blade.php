<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }

        .container {
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 30px 20px;
            margin: -20px -20px 20px -20px;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .header-right {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: top;
        }

        .company-name {
            font-size: 24pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .invoice-title {
            font-size: 18pt;
            font-weight: bold;
        }

        .invoice-number {
            font-size: 14pt;
            margin-top: 5px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 9pt;
            font-weight: bold;
            margin-top: 8px;
        }

        .status-paid {
            background-color: #10b981;
            color: white;
        }

        .status-pending {
            background-color: #f59e0b;
            color: white;
        }

        .status-overdue {
            background-color: #ef4444;
            color: white;
        }

        .billing-info {
            margin: 20px 0;
            display: table;
            width: 100%;
        }

        .billing-from, .billing-to {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 10px;
        }

        .billing-label {
            font-size: 9pt;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .billing-content {
            font-size: 10pt;
        }

        .company-title {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .invoice-details {
            background-color: #f3f4f6;
            padding: 15px;
            margin: 20px 0;
            display: table;
            width: 100%;
        }

        .detail-item {
            display: table-cell;
            width: 33.33%;
            padding: 0 10px;
        }

        .detail-label {
            font-size: 9pt;
            color: #666;
            margin-bottom: 3px;
        }

        .detail-value {
            font-size: 11pt;
            font-weight: bold;
        }

        .line-items {
            margin: 20px 0;
            width: 100%;
            border-collapse: collapse;
        }

        .line-items thead {
            background-color: #f3f4f6;
        }

        .line-items th {
            padding: 10px;
            text-align: left;
            font-size: 9pt;
            text-transform: uppercase;
            font-weight: bold;
            border-bottom: 2px solid #d1d5db;
        }

        .line-items td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10pt;
        }

        .line-items th.text-right,
        .line-items td.text-right {
            text-align: right;
        }

        .line-items th.text-center,
        .line-items td.text-center {
            text-align: center;
        }

        .item-description {
            font-weight: 600;
        }

        .item-details {
            font-size: 9pt;
            color: #666;
            margin-top: 3px;
        }

        .totals {
            margin: 20px 0 0 auto;
            width: 350px;
        }

        .total-row {
            display: table;
            width: 100%;
            padding: 8px 0;
        }

        .total-label {
            display: table-cell;
            text-align: left;
            font-size: 10pt;
        }

        .total-value {
            display: table-cell;
            text-align: right;
            font-size: 10pt;
            font-weight: 600;
        }

        .total-final {
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
        }

        .total-final .total-label {
            font-size: 12pt;
            font-weight: bold;
        }

        .total-final .total-value {
            font-size: 16pt;
            font-weight: bold;
            color: #2563eb;
        }

        .discount-row .total-value {
            color: #10b981;
        }

        .payment-info {
            background-color: #dbeafe;
            border-left: 4px solid #2563eb;
            padding: 15px;
            margin: 20px 0;
        }

        .payment-info h3 {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1e40af;
        }

        .payment-info p {
            font-size: 9pt;
            margin: 5px 0;
        }

        .notes {
            margin: 20px 0;
        }

        .notes h3 {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            color: #666;
        }

        .notes p {
            font-size: 9pt;
            color: #666;
        }

        .terms {
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
            margin-top: 30px;
        }

        .terms h3 {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            color: #666;
        }

        .terms ul {
            list-style: none;
            padding: 0;
        }

        .terms li {
            font-size: 8pt;
            color: #666;
            margin: 3px 0;
            padding-left: 12px;
            position: relative;
        }

        .terms li:before {
            content: "•";
            position: absolute;
            left: 0;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 9pt;
            color: #666;
        }

        .paid-stamp {
            position: absolute;
            top: 200px;
            right: 50px;
            transform: rotate(-15deg);
            border: 5px solid #10b981;
            color: #10b981;
            font-size: 36pt;
            font-weight: bold;
            padding: 20px 40px;
            opacity: 0.3;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="header-left">
                    <div class="company-name">{{ config('app.name') }}</div>
                    <div style="font-size: 9pt; margin-top: 3px;">{{ config('company.tagline', 'Healthcare Management Platform') }}</div>
                </div>
                <div class="header-right">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                    <span class="status-badge status-{{ $invoice->status }}">
                        {{ strtoupper($invoice->status) }}
                    </span>
                </div>
            </div>
        </div>

        @if($invoice->status === 'paid')
        <div class="paid-stamp">PAID</div>
        @endif

        <!-- Billing Information -->
        <div class="billing-info">
            <div class="billing-from">
                <div class="billing-label">From</div>
                <div class="billing-content">
                    <div class="company-title">{{ config('app.name') }}</div>
                    <div>{{ config('company.address', 'Company Address') }}</div>
                    <div>{{ config('company.city', 'City') }}, {{ config('company.province', 'Province') }}</div>
                    <div>{{ config('company.postal_code', 'Postal Code') }}</div>
                    <div style="margin-top: 5px;">{{ config('company.phone', 'Phone Number') }}</div>
                    <div>{{ config('company.email', 'email@company.com') }}</div>
                </div>
            </div>
            <div class="billing-to">
                <div class="billing-label">Bill To</div>
                <div class="billing-content">
                    <div class="company-title">{{ $invoice->tenant->name }}</div>
                    @if($invoice->tenant->contact_address)
                    <div>{{ $invoice->tenant->contact_address }}</div>
                    @endif
                    @if($invoice->tenant->contact_phone)
                    <div style="margin-top: 5px;">{{ $invoice->tenant->contact_phone }}</div>
                    @endif
                    @if($invoice->tenant->contact_email)
                    <div>{{ $invoice->tenant->contact_email }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="detail-item">
                <div class="detail-label">Invoice Date</div>
                <div class="detail-value">{{ $invoice->invoice_date->format('d M Y') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Due Date</div>
                <div class="detail-value" style="color: {{ $invoice->due_date->isPast() && $invoice->status !== 'paid' ? '#ef4444' : '#333' }}">
                    {{ $invoice->due_date->format('d M Y') }}
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Period</div>
                <div class="detail-value">{{ $invoice->period_start->format('d M Y') }} - {{ $invoice->period_end->format('d M Y') }}</div>
            </div>
        </div>

        <!-- Line Items -->
        <table class="line-items">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->line_items as $item)
                <tr>
                    <td>
                        <div class="item-description">{{ $item['description'] }}</div>
                        @if(isset($item['details']))
                        <div class="item-details">{{ $item['details'] }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item['quantity'] }}</td>
                    <td class="text-right">Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <div class="total-row">
                <div class="total-label">Subtotal</div>
                <div class="total-value">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</div>
            </div>

            @if($invoice->tax_amount > 0)
            <div class="total-row">
                <div class="total-label">Tax ({{ $invoice->tax_percentage }}%)</div>
                <div class="total-value">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</div>
            </div>
            @endif

            @if($invoice->discount_amount > 0)
            <div class="total-row discount-row">
                <div class="total-label">
                    Discount
                    @if($invoice->discount_code)
                    ({{ $invoice->discount_code }})
                    @endif
                </div>
                <div class="total-value">-Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</div>
            </div>
            @endif

            <div class="total-row total-final">
                <div class="total-label">Total</div>
                <div class="total-value">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</div>
            </div>

            @if($invoice->paid_amount > 0)
            <div class="total-row" style="color: #10b981;">
                <div class="total-label">Paid</div>
                <div class="total-value">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</div>
            </div>

            @if($invoice->paid_amount < $invoice->total_amount)
            <div class="total-row" style="color: #ef4444;">
                <div class="total-label">Balance Due</div>
                <div class="total-value">Rp {{ number_format($invoice->total_amount - $invoice->paid_amount, 0, ',', '.') }}</div>
            </div>
            @endif
            @endif
        </div>

        <!-- Payment Information -->
        @if($invoice->status === 'paid' && $invoice->paid_at)
        <div class="payment-info" style="background-color: #d1fae5; border-left-color: #10b981;">
            <h3 style="color: #047857;">Payment Received</h3>
            <p><strong>Paid on:</strong> {{ $invoice->paid_at->format('d M Y, H:i') }}</p>
            @if($invoice->payment_method)
            <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $invoice->payment_method)) }}</p>
            @endif
        </div>
        @elseif($invoice->status === 'pending' || $invoice->status === 'overdue')
        <div class="payment-info">
            <h3>Payment Instructions</h3>
            <p><strong>Bank Transfer:</strong></p>
            <p>Bank: {{ config('payment.bank_name', 'Bank Name') }}</p>
            <p>Account Name: {{ config('payment.account_name', 'Account Name') }}</p>
            <p>Account Number: {{ config('payment.account_number', 'Account Number') }}</p>
            <p style="margin-top: 8px;"><strong>Reference:</strong> {{ $invoice->invoice_number }}</p>
            <p style="font-size: 8pt; margin-top: 5px;">Please include the invoice number in your payment reference.</p>
        </div>
        @endif

        <!-- Notes -->
        @if($invoice->notes)
        <div class="notes">
            <h3>Notes</h3>
            <p>{{ $invoice->notes }}</p>
        </div>
        @endif

        <!-- Terms & Conditions -->
        <div class="terms">
            <h3>Terms & Conditions</h3>
            <ul>
                <li>Payment is due within {{ $invoice->payment_terms ?? 14 }} days from the invoice date.</li>
                <li>Late payments may incur additional charges.</li>
                <li>All prices are in Indonesian Rupiah (IDR).</li>
                <li>For questions about this invoice, please contact {{ config('company.email', 'billing@company.com') }}</li>
            </ul>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
            <p style="margin-top: 5px;">{{ config('app.name') }} - {{ config('app.url') }}</p>
        </div>
    </div>
</body>
</html>
