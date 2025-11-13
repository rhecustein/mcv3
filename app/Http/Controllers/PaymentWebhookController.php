<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    protected $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }

    /**
     * Handle Midtrans webhook notification
     */
    public function midtransNotification(Request $request)
    {
        try {
            $notification = $request->all();

            Log::info('Midtrans notification received', $notification);

            $success = $this->midtrans->handleNotification($notification);

            if ($success) {
                return response()->json(['status' => 'success'], 200);
            }

            return response()->json(['status' => 'error'], 400);
        } catch (\Exception $e) {
            Log::error('Midtrans webhook exception: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
