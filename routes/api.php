<?php

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/doku/webhook', function (Request $request) {
    Log::info('📩 Doku Webhook Received:', $request->all());

    // Ambil data dari request
    $data = $request->all();

    // 🛑 Validasi request (Pastikan ada key yang dibutuhkan)
    if (!isset($data['transaction']) || !isset($data['order'])) {
        Log::error('🚨 Invalid Doku Webhook Data:', $data);
        return Response::json(['message' => 'Invalid request'], 400);
    }

    $order_id = $data['order']['invoice_number'];
    $status = $data['transaction']['status'];
    $requestTarget = "/notification/v1/transactions";

    // 🔐 Validasi Signature
    $clientId = 'BRN-0240-1738824871672'; // Sesuaikan dengan Client ID Doku
    $sharedKey = "SK-od4DFqAOYdoFRGVQEMUV"; // Sesuaikan dengan Shared Key
    $requestId = $request->header('Request-Id');
    $requestTimestamp = $request->header('Request-Timestamp');
    $digest = base64_encode(hash('sha256', json_encode($data), true));

    $signatureRaw = "Client-Id:$clientId\nRequest-Id:$requestId\nRequest-Timestamp:$requestTimestamp\nRequest-Target:$requestTarget\nDigest:$digest";
    $calculatedSignature = base64_encode(hash_hmac('sha256', $signatureRaw, $sharedKey, true));

    if ($request->header('Signature') !== "HMACSHA256=$calculatedSignature") {
        Log::error('🚨 Invalid Signature for Doku Webhook:', ['received' => $request->header('Signature'), 'expected' => "HMACSHA256=$calculatedSignature"]);
        return Response::json(['message' => 'Invalid signature'], 403);
    }

    // 🚀 Update status transaksi di database
    $transaction = Transaction::where('invoice_number', $order_id)->first();

    if (!$transaction) {
        Log::error("🚨 Transaction not found for invoice: $order_id");
        return Response::json(['message' => 'Transaction not found'], 404);
    }

    // 🔄 Update status berdasarkan response dari Doku
    if ($status === 'SUCCESS') {
        $transaction->update(['status' => 'paid']);
    } elseif ($status === 'FAILED') {
        $transaction->update(['status' => 'failed']);
    } elseif ($status === 'PENDING') {
        $transaction->update(['status' => 'pending']);
    }

    Log::info("✅ Transaction $order_id updated to $status");

    return Response::json(['message' => 'Notification received successfully'], 200);
});