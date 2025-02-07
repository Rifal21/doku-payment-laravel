<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction; // pastikan model Transaction ada
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function handleCheckout(Request $request)
    {
        // Simpan transaksi ke database
        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'cart_items' => json_encode($request->cart_items),
            'total_price' => $request->total_price,
            'invoice_number' => $request->invoice_number,
            'status' => 'PENDING',
        ]);

        if ($transaction) {
            // Mengirim permintaan ke DOKU untuk pembayaran
            $paymentResponse = $this->processPaymentRequest(
                $request->invoice_number,
                $request->total_price,
                Auth::user()->name,
                Auth::user()->email,
                "081234567890"
            );
            // dd($paymentResponse); 
            if (isset($paymentResponse['response']['payment']['url'])) {
                return response()->json([
                    'success' => true,
                    'payment_url' => $paymentResponse['response']['payment']['url'],
                ]);
            } else {
                \Illuminate\Support\Facades\Log::error('Payment response invalid', ['response' => $paymentResponse]);
                return response()->json(['success' => false, 'error' => 'Invalid payment response'], 500);
            }
            
        }

        return response()->json(['success' => false], 500);
    }

    // private function processPaymentRequest($invoice, $orderAmount, $customerName, $customerEmail, $phone)
    // {
    //     // $clientId = env('DOKU_CLIENT_ID');
    //     $clientId = 'BRN-0240-1738824871672';
    //     // $sharedKey = env('DOKU_SHARED_KEY');
    //     $sharedKey = "SK-od4DFqAOYdoFRGVQEMUV";
    //     $requestTarget = "/checkout/v1/payment";
    //     $requestDate = now()->format('Y-m-d\TH:i:s\Z');
    //     $environmentUrl = 'https://sandbox.doku.com';
    
    //     // dd($clientId, $sharedKey, $requestTarget, $requestDate, $environmentUrl);
    //     $body = json_encode([
    //         'customer' => [
    //             'id' => Auth::id(),
    //             'name' => $customerName,
    //             'email' => $customerEmail,
    //             'phone' => $phone,
    //         ],
    //         'order' => [
    //             'amount' => $orderAmount,
    //             'callback_url' => url('/'), // Mengarahkan callback ke halaman utama
    //             'currency' => "IDR",
    //             'invoice_number' => $invoice,
    //         ],
    //         'payment' => [
    //             'payment_due_date' => 60,
    //         ],
    //     ]);
    
    //     $digest = base64_encode(hash('sha256', $body, true));
    //     $signatureRaw = "Client-Id:$clientId\nRequest-Id:$invoice\nRequest-Timestamp:$requestDate\nRequest-Target:$requestTarget\nDigest:$digest";
    //     $signature = base64_encode(hash_hmac('sha256', $signatureRaw, $sharedKey, true));
    
    //     $headers = [
    //         'Content-Type' => 'application/json',
    //         'Client-Id' => $clientId,
    //         'Request-Id' => $invoice,
    //         'Request-Timestamp' => $requestDate,
    //         'Digest' => $digest,
    //         'Signature' => "HMACSHA256=$signature",
    //     ];

    //     // dd($headers);
    
    //     $response = Http::withHeaders($headers)->post($environmentUrl . $requestTarget, json_decode($body, true));
    //     return $response->json();
    // }
    
    private function processPaymentRequest($invoice, $orderAmount, $customerName, $customerEmail, $phone)
    {
        $clientId = env('DOKU_CLIENT_ID');
        $sharedKey = env('DOKU_SHARED_KEY');
        $requestId = $invoice;
        $requestTarget = "/checkout/v1/payment";
        $requestTimestamp = now()->format('Y-m-d\TH:i:s\Z');
        $environmentUrl = 'https://api-sandbox.doku.com';

        // 🛒 Detail pesanan
        $orderLines = [
            [
                'name' => 'Produk Digital', 
                'price' => $orderAmount, 
                'quantity' => 1
            ]
        ];

        // 📦 Membuat payload request
        $body = [
            'customer' => [
                'id' => Auth::id(),
                'name' => $customerName,
                'email' => $customerEmail,
                'phone' => $phone,
            ],
            'order' => [
                'amount' => $orderAmount,
                'currency' => "IDR",
                'invoice_number' => $invoice,
                'callback_url' => url('/api/doku/webhook'), 
                'return_url' => url('/payment/success'),     
                'lines' => $orderLines
            ],
            'payment' => [
                'payment_due_date' => 60,
                'payment_method_types' => ["VIRTUAL_ACCOUNT", "CREDIT_CARD", "QRIS"]
            ],
        ];

        // 🔐 Membuat Signature
        $bodyJson = json_encode($body, JSON_UNESCAPED_SLASHES);
        // 🔹 Hash body untuk mendapatkan digest
    $digest = base64_encode(hash('sha256', $bodyJson, true));

    // 🔹 Format signature sesuai dokumentasi
    $signatureRaw = implode("\n", [
        "Client-Id:$clientId",
        "Request-Id:$requestId",
        "Request-Timestamp:$requestTimestamp",
        "Request-Target:$requestTarget",
        // "Digest:$digest"
    ]);

    // 🔹 Generate signature HMAC SHA256
    $signature = base64_encode(hash_hmac('sha256', $signatureRaw, $sharedKey, true));

    // 🔹 Buat headers untuk request
    $headers = [
        'Content-Type' => 'application/json',
        'Client-Id' => $clientId,
        'Request-Id' => $requestId,
        'Request-Timestamp' => $requestTimestamp,
        // 'Digest' => $digest,
        'Signature' => "HMACSHA256=$signature",
    ];
    // dd($headers);   

    // 🛠 Debugging jika perlu
    Log::info('DOKU Payment Request:', compact('headers', 'body', 'signatureRaw', 'signature'));

    // 🔹 Kirim request ke DOKU API
    $response = Http::withHeaders($headers)->post($environmentUrl . $requestTarget, $body);

        // 🚀 Logging untuk debugging
        Log::info('DOKU Payment Request:', [
            'headers' => $headers,
            'body' => $body,
            'response' => $response->json(),
        ]);

        // dd($response->json());
        return $response->json();
    }

    public function handleCallback(Request $request)
    {
        Log::info('🔔 Callback received:', $request->all());

        // Validasi struktur data yang masuk
        if (!$request->has('order') || !isset($request->order['invoice_number'])) {
            return response()->json(['success' => false, 'message' => 'Invoice number missing'], 400);
        }

        $invoiceNumber = $request->order['invoice_number'];
        $status = $request->order['status'] ?? 'PENDING';

        // Ambil transaksi berdasarkan invoice_number
        $transaction = Transaction::where('invoice_number', $invoiceNumber)->first();

        if ($transaction) {
            // Update status transaksi sesuai data dari DOKU
            $transaction->update(['status' => strtoupper($status)]);

            return response()->json(['success' => true, 'message' => 'Transaction updated successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
    }

    public function handleSuccess(Request $request)
    {
        Log::info('✅ Success response received:', $request->all());

        // Validasi request
        if (!$request->has('order') || !isset($request->order['invoice_number'])) {
            return response()->json(['success' => false, 'message' => 'Invoice number missing'], 400);
        }

        $invoiceNumber = $request->order['invoice_number'];

        // Ambil transaksi berdasarkan invoice_number
        $transaction = Transaction::where('invoice_number', $invoiceNumber)->first();

        if ($transaction) {
            // Update status transaksi menjadi SUCCESS
            $transaction->update(['status' => 'SUCCESS']);

            return response()->json(['success' => true, 'message' => 'Transaction updated successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
    }
}
    

