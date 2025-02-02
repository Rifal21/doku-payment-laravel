<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function index()
    {
        $cartItems = session()->get('cart', []);
        $totalPrice = array_sum(array_map(fn ($item) => $item['price'] * $item['quantity'], $cartItems));

        return view('transaction.index', compact('cartItems', 'totalPrice'));
    }

    // public function pay(Request $request)
    // {
    //     // dd($request->all());
    //     // Konfigurasi DOKU
    //     $clientId = env('DOKU_CLIENT_ID');
    //     $sharedKey = env('DOKU_SHARED_KEY');
    //     $paymentUrl = 'https://api-sandbox.doku.com/checkout/v1/payment';
    
    //     $orderId = uniqid();
    //     $amount = number_format($request->total_price, 2, '.', '');
    //     $email = $request->user()->email;
    
    //     // Request Timestamp sesuai format ISO 8601
    //     $timestamp = Carbon::now()->format('Y-m-d\TH:i:s\Z');
    //     $requestId = uniqid();
    
    //     // Membuat Signature HMAC SHA256
    //     $signaturePayload = "Client-Id:$clientId\nRequest-Id:$requestId\nRequest-Timestamp:$timestamp\nRequest-Target:/checkout/v1/payment\n$sharedKey";
    //     $signature = hash_hmac('sha256', $signaturePayload, $sharedKey);
    
    //     // Request payload
    //     $payload = [
    //         'order' => [
    //             'invoice_number' => $orderId,
    //             'amount' => $amount,
    //         ],
    //         'customer' => [
    //             'email' => $email,
    //         ],
    //     ];
    
    //     // Kirim permintaan ke DOKU API
    //     $response = Http::withHeaders([
    //         'Client-Id' => $clientId,
    //         'Request-Id' => $requestId,
    //         'Request-Timestamp' => $timestamp,
    //         'Signature' => "HMACSHA256=$signature",
    //         'Content-Type' => 'application/json'
    //     ])->post($paymentUrl, $payload);

    //     dd($response);
    
    //     // Debugging log (opsional)
    //     Log::info('DOKU Payment Request', ['payload' => $payload, 'headers' => [
    //         'Client-Id' => $clientId,
    //         'Request-Id' => $requestId,
    //         'Request-Timestamp' => $timestamp,
    //         'Signature' => "HMACSHA256=$signature",
    //     ]]);
    
    //     if ($response->successful()) {
    //         $responseData = $response->json();
    //         return redirect()->away($responseData['redirect_url']);
    //     } else {
    //         Log::error('DOKU Payment Error', ['response' => $response->json()]);
    //         return back()->withErrors(['payment' => 'Gagal melakukan pembayaran.']);
    //     }
    // }
    

}
