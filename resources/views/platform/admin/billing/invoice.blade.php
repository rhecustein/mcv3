<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none;
            }
            body {
                background: white;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation (Hidden on Print) -->
    <div class="no-print">
        @include('platform.admin.partials.nav')
    </div>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Actions Bar (Hidden on Print) -->
        <div class="no-print mb-6 flex items-center justify-between">
            <a href="{{ route('platform.admin.tenants.show', $invoice->tenant) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                ← Back to Tenant
            </a>
            <div class="flex space-x-3">
                <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition duration-150">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </button>
                <a href="{{ route('platform.admin.billing.invoice.pdf', $invoice) }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold transition duration-150">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download PDF
                </a>
                @if($invoice->status === 'pending' || $invoice->status === 'overdue')
                <form action="{{ route('platform.admin.billing.invoice.mark-paid', $invoice) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold transition duration-150">
                        Mark as Paid
                    </button>
                </form>
                @endif
            </div>
        </div>

        <!-- Invoice Document -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Invoice Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-8 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold mb-2">INVOICE</h1>
                        <p class="text-blue-100">{{ config('app.name') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold">{{ $invoice->invoice_number }}</p>
                        <span class="inline-block mt-2 px-3 py-1 text-sm font-semibold rounded-full
                            @if($invoice->status === 'paid') bg-green-500
                            @elseif($invoice->status === 'pending') bg-yellow-500
                            @elseif($invoice->status === 'overdue') bg-red-500
                            @elseif($invoice->status === 'cancelled') bg-gray-500
                            @endif">
                            {{ strtoupper($invoice->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <!-- From/To Information -->
                <div class="grid grid-cols-2 gap-8 mb-8">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase mb-3">From</h3>
                        <div class="text-gray-900">
                            <p class="font-bold text-lg">{{ config('app.name') }}</p>
                            <p class="text-sm mt-2">{{ config('company.address', 'Company Address') }}</p>
                            <p class="text-sm">{{ config('company.city', 'City') }}, {{ config('company.province', 'Province') }}</p>
                            <p class="text-sm">{{ config('company.postal_code', 'Postal Code') }}</p>
                            <p class="text-sm mt-2">{{ config('company.phone', 'Phone Number') }}</p>
                            <p class="text-sm">{{ config('company.email', 'email@company.com') }}</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase mb-3">Bill To</h3>
                        <div class="text-gray-900">
                            <p class="font-bold text-lg">{{ $invoice->tenant->name }}</p>
                            @if($invoice->tenant->contact_address)
                            <p class="text-sm mt-2">{{ $invoice->tenant->contact_address }}</p>
                            @endif
                            @if($invoice->tenant->contact_phone)
                            <p class="text-sm mt-2">{{ $invoice->tenant->contact_phone }}</p>
                            @endif
                            @if($invoice->tenant->contact_email)
                            <p class="text-sm">{{ $invoice->tenant->contact_email }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Invoice Details -->
                <div class="grid grid-cols-3 gap-6 mb-8 p-6 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Invoice Date</p>
                        <p class="font-semibold text-gray-900">{{ $invoice->invoice_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Due Date</p>
                        <p class="font-semibold {{ $invoice->due_date->isPast() && $invoice->status !== 'paid' ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $invoice->due_date->format('d M Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Period</p>
                        <p class="font-semibold text-gray-900">
                            {{ $invoice->period_start->format('d M Y') }} - {{ $invoice->period_end->format('d M Y') }}
                        </p>
                    </div>
                </div>

                <!-- Line Items -->
                <div class="mb-8">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-300">
                                <th class="text-left py-3 text-sm font-semibold text-gray-700 uppercase">Description</th>
                                <th class="text-center py-3 text-sm font-semibold text-gray-700 uppercase">Quantity</th>
                                <th class="text-right py-3 text-sm font-semibold text-gray-700 uppercase">Unit Price</th>
                                <th class="text-right py-3 text-sm font-semibold text-gray-700 uppercase">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->line_items as $item)
                            <tr class="border-b border-gray-200">
                                <td class="py-4">
                                    <p class="font-medium text-gray-900">{{ $item['description'] }}</p>
                                    @if(isset($item['details']))
                                    <p class="text-sm text-gray-600 mt-1">{{ $item['details'] }}</p>
                                    @endif
                                </td>
                                <td class="text-center py-4 text-gray-900">{{ $item['quantity'] }}</td>
                                <td class="text-right py-4 text-gray-900">Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</td>
                                <td class="text-right py-4 font-semibold text-gray-900">Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="flex justify-end mb-8">
                    <div class="w-80">
                        <div class="flex justify-between py-2 text-gray-700">
                            <span>Subtotal</span>
                            <span class="font-semibold">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                        </div>

                        @if($invoice->tax_amount > 0)
                        <div class="flex justify-between py-2 text-gray-700">
                            <span>Tax ({{ $invoice->tax_percentage }}%)</span>
                            <span class="font-semibold">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        @if($invoice->discount_amount > 0)
                        <div class="flex justify-between py-2 text-green-600">
                            <span>Discount
                                @if($invoice->discount_code)
                                ({{ $invoice->discount_code }})
                                @endif
                            </span>
                            <span class="font-semibold">-Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        <div class="border-t-2 border-gray-300 mt-2 pt-2">
                            <div class="flex justify-between py-2">
                                <span class="text-lg font-bold text-gray-900">Total</span>
                                <span class="text-2xl font-bold text-blue-600">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        @if($invoice->paid_amount > 0)
                        <div class="flex justify-between py-2 text-green-600">
                            <span class="font-semibold">Paid</span>
                            <span class="font-bold">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
                        </div>

                        @if($invoice->paid_amount < $invoice->total_amount)
                        <div class="flex justify-between py-2 text-red-600">
                            <span class="font-semibold">Balance Due</span>
                            <span class="font-bold">Rp {{ number_format($invoice->total_amount - $invoice->paid_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>

                <!-- Payment Information -->
                @if($invoice->status === 'paid' && $invoice->paid_at)
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-8 rounded">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="font-semibold text-green-900">Payment Received</p>
                            <p class="text-sm text-green-700">Paid on {{ $invoice->paid_at->format('d M Y, H:i') }}</p>
                            @if($invoice->payment_method)
                            <p class="text-sm text-green-700">Payment Method: {{ ucfirst(str_replace('_', ' ', $invoice->payment_method)) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @elseif($invoice->status === 'overdue')
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-8 rounded">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="font-semibold text-red-900">Payment Overdue</p>
                            <p class="text-sm text-red-700">This invoice was due on {{ $invoice->due_date->format('d M Y') }}</p>
                            <p class="text-sm text-red-700">Please contact us to arrange payment.</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Payment Instructions (for unpaid invoices) -->
                @if($invoice->status === 'pending' || $invoice->status === 'overdue')
                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-8 rounded">
                    <h3 class="font-semibold text-blue-900 mb-3">Payment Instructions</h3>
                    <div class="text-sm text-blue-800 space-y-2">
                        <p><strong>Bank Transfer:</strong></p>
                        <p>Bank: {{ config('payment.bank_name', 'Bank Name') }}</p>
                        <p>Account Name: {{ config('payment.account_name', 'Account Name') }}</p>
                        <p>Account Number: {{ config('payment.account_number', 'Account Number') }}</p>
                        <p class="mt-3"><strong>Reference:</strong> {{ $invoice->invoice_number }}</p>
                        <p class="mt-3 text-xs">Please include the invoice number in your payment reference.</p>
                    </div>
                </div>
                @endif

                <!-- Notes -->
                @if($invoice->notes)
                <div class="mb-8">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase mb-2">Notes</h3>
                    <p class="text-gray-600 text-sm">{{ $invoice->notes }}</p>
                </div>
                @endif

                <!-- Terms & Conditions -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase mb-2">Terms & Conditions</h3>
                    <div class="text-xs text-gray-600 space-y-1">
                        <p>• Payment is due within {{ $invoice->payment_terms ?? 14 }} days from the invoice date.</p>
                        <p>• Late payments may incur additional charges.</p>
                        <p>• All prices are in Indonesian Rupiah (IDR).</p>
                        <p>• For questions about this invoice, please contact {{ config('company.email', 'billing@company.com') }}</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-8 pt-6 border-t border-gray-200 text-center text-sm text-gray-600">
                    <p>Thank you for your business!</p>
                    <p class="mt-2">{{ config('app.name') }} - {{ config('app.url') }}</p>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        @if($invoice->payments && $invoice->payments->count() > 0)
        <div class="no-print mt-8 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Payment History</h3>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($invoice->payments as $payment)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $payment->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $payment->payment_number }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right font-semibold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($payment->status === 'paid') bg-green-100 text-green-800
                                @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($payment->status === 'failed') bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </main>
</body>
</html>
