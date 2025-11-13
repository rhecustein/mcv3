<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\McuBooking;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected $serverKey;
    protected $clientKey;
    protected $isProduction;
    protected $apiUrl;

    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key');
        $this->clientKey = config('services.midtrans.client_key');
        $this->isProduction = config('services.midtrans.is_production', false);
        $this->apiUrl = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    /**
     * Create Snap transaction token
     */
    public function createSnapToken(Payment $payment): ?string
    {
        try {
            $payable = $payment->payable; // Get McuBooking or Invoice

            $transactionDetails = [
                'order_id' => $payment->payment_number,
                'gross_amount' => (int) $payment->amount,
            ];

            $itemDetails = [[
                'id' => $payment->payable_id,
                'price' => (int) $payment->amount,
                'quantity' => 1,
                'name' => $this->getItemName($payment),
            ]];

            $customerDetails = $this->getCustomerDetails($payment);

            $params = [
                'transaction_details' => $transactionDetails,
                'item_details' => $itemDetails,
                'customer_details' => $customerDetails,
                'enabled_payments' => $this->getEnabledPayments($payment->payment_method),
                'callbacks' => [
                    'finish' => route('mcu.bookings.show', $payment->payable_id),
                ],
            ];

            $response = Http::withBasicAuth($this->serverKey, '')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->apiUrl, $params);

            if ($response->successful()) {
                $data = $response->json();

                // Update payment with gateway info
                $payment->update([
                    'gateway_transaction_id' => $data['token'] ?? null,
                    'gateway_payment_url' => $data['redirect_url'] ?? null,
                    'gateway_response' => $data,
                ]);

                return $data['token'] ?? null;
            }

            Log::error('Midtrans create snap token failed', [
                'response' => $response->json(),
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Midtrans exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get transaction status from Midtrans
     */
    public function getTransactionStatus(string $orderId): ?array
    {
        try {
            $url = $this->isProduction
                ? "https://api.midtrans.com/v2/{$orderId}/status"
                : "https://api.sandbox.midtrans.com/v2/{$orderId}/status";

            $response = Http::withBasicAuth($this->serverKey, '')
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Midtrans get status exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Handle Midtrans notification/webhook
     */
    public function handleNotification(array $notification): bool
    {
        try {
            $orderId = $notification['order_id'] ?? null;
            $transactionStatus = $notification['transaction_status'] ?? null;
            $fraudStatus = $notification['fraud_status'] ?? null;

            if (!$orderId) {
                return false;
            }

            $payment = Payment::where('payment_number', $orderId)->first();

            if (!$payment) {
                Log::warning('Payment not found for Midtrans notification', ['order_id' => $orderId]);
                return false;
            }

            // Verify signature
            if (!$this->verifySignature($notification)) {
                Log::error('Invalid Midtrans signature', ['order_id' => $orderId]);
                return false;
            }

            // Update payment based on transaction status
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $this->handleSuccessPayment($payment, $notification);
                }
            } elseif ($transactionStatus == 'settlement') {
                $this->handleSuccessPayment($payment, $notification);
            } elseif ($transactionStatus == 'pending') {
                $payment->update([
                    'status' => 'pending',
                    'gateway_response' => $notification,
                ]);
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $payment->update([
                    'status' => 'failed',
                    'gateway_response' => $notification,
                ]);

                // Update booking status if applicable
                if ($payment->payable_type === McuBooking::class) {
                    $payment->payable->update(['payment_status' => 'failed']);
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Midtrans handle notification exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Handle successful payment
     */
    protected function handleSuccessPayment(Payment $payment, array $notification): void
    {
        $payment->markAsSuccess($notification);

        // Update booking if payment is for MCU booking
        if ($payment->payable_type === McuBooking::class) {
            $booking = $payment->payable;
            $booking->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'status' => 'confirmed', // Auto-confirm when paid
            ]);

            // Send confirmation email (will be implemented later)
            // event(new BookingConfirmed($booking));
        }
    }

    /**
     * Verify Midtrans signature
     */
    protected function verifySignature(array $notification): bool
    {
        $orderId = $notification['order_id'] ?? '';
        $statusCode = $notification['status_code'] ?? '';
        $grossAmount = $notification['gross_amount'] ?? '';

        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);

        return $signatureKey === ($notification['signature_key'] ?? '');
    }

    /**
     * Get item name for transaction
     */
    protected function getItemName(Payment $payment): string
    {
        if ($payment->payable_type === McuBooking::class) {
            $booking = $payment->payable;
            return "MCU Package - {$booking->package->name}";
        }

        return "Invoice #{$payment->payable_id}";
    }

    /**
     * Get customer details
     */
    protected function getCustomerDetails(Payment $payment): array
    {
        if ($payment->payable_type === McuBooking::class) {
            $booking = $payment->payable;
            return [
                'first_name' => $booking->patient_name,
                'email' => $booking->patient_email,
                'phone' => $booking->patient_phone,
            ];
        }

        $tenant = $payment->tenant;
        return [
            'first_name' => $tenant->name,
            'email' => $tenant->email,
            'phone' => $tenant->phone ?? '',
        ];
    }

    /**
     * Get enabled payments based on method
     */
    protected function getEnabledPayments(string $method): array
    {
        return match($method) {
            'credit_card' => ['credit_card'],
            'bank_transfer' => ['bca_va', 'bni_va', 'bri_va', 'permata_va', 'other_va'],
            'e_wallet' => ['gopay', 'shopeepay'],
            'qris' => ['qris'],
            default => ['credit_card', 'gopay', 'shopeepay', 'qris', 'bca_va', 'bni_va'],
        };
    }
}
