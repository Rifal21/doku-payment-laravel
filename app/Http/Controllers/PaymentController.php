<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function processPayment(Request $request)
    {
        $amount = $request->input('amount');
        $orderId = 'INV-' . time();

        $data = [
            'client_id' => env('DOKU_CLIENT_ID'),
            'merchant_id' => env('DOKU_MERCHANT_ID'),
            'amount' => $amount,
            'order_id' => $orderId,
            'payment_method' => 'DOKU Wallet', // contoh metode pembayaran
            'payment_url' => route('products.index'),
        ];

        dd($data);

        // Request ke API DOKU untuk mendapatkan URL pembayaran
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Client-Id' => env('DOKU_CLIENT_ID'),
            'Request-Id' => uniqid(),
            'Signature' => $this->generateSignature($data),
        ])->timeout(60)
        ->post('https://api-sandbox.doku.com/checkout/v1/payment', $data);

        dd($response);
        $responseBody = json_decode($response->body(), true);

        if ($response->successful() && isset($responseBody['payment_url'])) {
            return response()->json([
                'payment_url' => $responseBody['payment_url']
            ]);
        }

        return response()->json(['error' => 'Failed to process payment'], 400);
    }

//     public function processPayment(Request $request)
// {
//     $amount = $request->amount;

//     // Simulasi request ke DOKU API untuk mendapatkan payment URL atau token
//     $response = Http::withHeaders([
//         'Content-Type' => 'application/json',
//         'Client-Id' => env('DOKU_CLIENT_ID'),
//         'Request-Id' => uniqid(),
//     ])->post('https://api-sandbox.doku.com/checkout/v1/payment', [
//         'order' => [
//             'amount' => $amount,
//             'invoice_number' => 'INV-' . uniqid(),
//         ],
//     ]);

//     if ($response->successful()) {
//         return response()->json([
//             'payment_data' => $response->json()
//         ]);
//     } else {
//         return response()->json(['error' => 'Payment initialization failed'], 500);
//     }
// }


    private function generateSignature($data)
    {
        $secretKey = env('DOKU_SECRET_KEY');
        $payload = json_encode($data);
        return hash_hmac('sha256', $payload, $secretKey);
    }
    
    public function handleCallback(Request $request)
{
    $data = $request->all();
    // Validasi dan proses status pembayaran
    if ($data['status'] == 'SUCCESS') {
        // Simpan transaksi sukses
        return redirect()->route('order.success')->with('success', 'Pembayaran berhasil!');
    }
    return redirect()->route('cart.index')->with('error', 'Pembayaran gagal.');
}

}
