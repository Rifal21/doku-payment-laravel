<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private function generateSignature($clientId, $requestId, $requestDate, $requestTarget, $body, $sharedKey)
    {
        // Gunakan JSON_ENCODE dengan JSON_UNESCAPED_SLASHES untuk format yang lebih sesuai
        $bodyJson = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // Generate Digest
        $digestValue = base64_encode(hash('sha256', $bodyJson, true));

        // Komponen Signature
        $componentSignature = implode("\n", [
            "Client-Id:$clientId",
            "Request-Id:$requestId",
            "Request-Timestamp:$requestDate",
            "Request-Target:$requestTarget",
            "Digest:$digestValue"
        ]);

        // Generate Signature dengan HMAC-SHA256
        $signature = base64_encode(hash_hmac('sha256', $componentSignature, $sharedKey, true));

        return [
            'digest' => $digestValue,
            'signature' => $signature,
            'raw_signature' => $componentSignature
        ];
    }

    public function getPaymentHeaders(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'invoice_number' => 'required|string|unique:transactions,invoice_number',
        ]);

        $amount = 0;
        $orderLines = [];

        foreach ($validated['items'] as $item) {
            
            $product = Product::find($item['product_id']);
            if (!$product) {
                return response()->json(['error' => 'Product not found'], 404);
            }

            $subtotal = $product->price * $item['quantity'];
            $amount += $subtotal;

            $orderLines[] = [
                'name' => $product->name,
                'price' => intval($product->price),
                'quantity' => $item['quantity']
            ];
        }

        // Validasi ulang amount untuk mencegah manipulasi dari frontend
        if ($request->input('amount') != $amount) {
            return response()->json(['error' => 'Invalid total amount'], 400);
        }

        // Simpan transaksi di database
       Transaction::create([
            'user_id' => Auth::id(),
            'cart_items' => json_encode($validated['items']),
            'total_price' => $amount,
            'invoice_number' => $validated['invoice_number'],
            'status' => 'PENDING',
        ]);

        // Konfigurasi Doku Payment
        $clientId = env('DOKU_CLIENT_ID');
        $sharedKey = env('DOKU_SHARED_KEY');
        $requestId = $validated['invoice_number'];
        $requestTarget = "/checkout/v1/payment";
        $requestTimestamp = Carbon::now()->format('Y-m-d\TH:i:s\Z');

        $body = [
            'customer' => [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => Auth::user()->phone ?? '081234567890',
            ],
            'order' => [
                'amount' => $amount,
                'currency' => "IDR",
                'invoice_number' => $validated['invoice_number'],
                'callback_url' => url('/payment/callback'),
                'return_url' => url('/payment/success'),
                'line_items' => $orderLines,
            ],
            'payment' => [
                'payment_due_date' => 60,
                'type' => "SALE",
                'payment_method_types' => [               
                "JENIUS_PAY",
                "ONLINE_TO_OFFLINE_ALFA",
                "OCTO_CLICKS",
                "PEER_TO_PEER_KREDIVO",
                "VIRTUAL_ACCOUNT_BCA",
                "CREDIT_CARD",
                "EMONEY_OVO",
                "ONLINE_TO_OFFLINE_INDOMARET",
                "EMONEY_DOKU",
                "VIRTUAL_ACCOUNT_BANK_MANDIRI",
                "EPAY_BRI",
                "PEER_TO_PEER_INDODANA",
                "VIRTUAL_ACCOUNT_BRI",
                "EMONEY_LINKAJA",
                "EMONEY_SHOPEE_PAY",
                "VIRTUAL_ACCOUNT_BNI",
                "VIRTUAL_ACCOUNT_BANK_PERMATA",
                "VIRTUAL_ACCOUNT_DOKU",
                "VIRTUAL_ACCOUNT_BANK_CIMB",
                "VIRTUAL_ACCOUNT_BANK_DANAMON",
                "VIRTUAL_ACCOUNT_BANK_SYARIAH_MANDIRI",
                "VIRTUAL_ACCOUNT_MAYBANK",
                "DIRECT_DEBIT_CIMB",
                "EMONEY_DANA",
                "DIRECT_DEBIT_BRI",
                "DIRECT_DEBIT_ALLO",
                "PEER_TO_PEER_BRI_CERIA",
                "VIRTUAL_ACCOUNT_BNC",
                "PERMATA_NET",
                "KLIKPAY_BCA",
                "VIRTUAL_ACCOUNT_BTN",
                "DANAMON_ONLINE_BANKING",
                "VIRTUAL_ACCOUNT_SINARMAS"]
            ],
        ];
        
        // Generate Signature
        $signatureData = $this->generateSignature($clientId, $requestId, $requestTimestamp, $requestTarget, $body, $sharedKey);

        return response()->json([
            'headers' => [
                'Content-Type' => 'application/json',
                'Client-Id' => $clientId,
                'Request-Id' => $requestId,
                'Request-Timestamp' => $requestTimestamp,
                'Digest' => $signatureData['digest'],
                'Signature' => "HMACSHA256=" . $signatureData['signature'],
            ],
            'body' => $body,
            'request_target' => $requestTarget
        ]);
    }

    public function handleCallback()
    {
        return view('payment.callback');
    }
}
