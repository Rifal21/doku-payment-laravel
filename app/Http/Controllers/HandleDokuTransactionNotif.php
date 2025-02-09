<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;

class HandleDokuTransactionNotif extends Controller
{
    public function handleNotification(Request $request)
    {
        // dd($request->all());
        Log::info('📩 Doku Webhook Received:', $request->all());

        $data = $request->all();

        if (!isset($data['transaction']) || !isset($data['order'])) {
            Log::error('🚨 Invalid Doku Webhook Data:', $data);
            return response()->json(['message' => 'Invalid request'], 400);
        }

        $invoiceNumber = $data['order']['invoice_number'];
        $status = $data['transaction']['status'];

        $transaction = Transaction::where('invoice_number', $invoiceNumber)->first();

        if (!$transaction) {
            Log::error("🚨 Transaction not found for invoice: $invoiceNumber");
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        switch ($status) {
            case 'SUCCESS':
                $transaction->update(['status' => 'paid']);
                break;
            case 'FAILED':
                $transaction->update(['status' => 'failed']);
                break;
            case 'PENDING':
                $transaction->update(['status' => 'pending']);
                break;
        }

        Log::info("✅ Transaction $invoiceNumber updated to $status");

        return response()->json(['message' => 'Notification received successfully'], 200);
    }
}
