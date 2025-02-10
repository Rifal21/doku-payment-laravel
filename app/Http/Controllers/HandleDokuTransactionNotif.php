<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;

class HandleDokuTransactionNotif extends Controller
{
    public function handleNotification(Request $request)
    {
        // $data = $request->getContent();


        $notificationHeader = getallheaders();
        $notificationBody = file_get_contents('php://input');
            $notificationPath = '/api/doku/notif-hook'; // Adjust according to your notification path
            $secretKey = env('DOKU_SHARED_KEY'); // Adjust according to your secret key
            
            $digest = base64_encode(hash('sha256', $notificationBody, true));
            $rawSignature = "Client-Id:" . $notificationHeader['Client-Id'] . "\n"
            . "Request-Id:" . $notificationHeader['Request-Id'] . "\n"
            . "Request-Timestamp:" . $notificationHeader['Request-Timestamp'] . "\n"
            . "Request-Target:" . $notificationPath . "\n"
            . "Digest:" . $digest;
            
            $signature = base64_encode(hash_hmac('sha256', $rawSignature, $secretKey, true));
            // dd($signature);
            $finalSignature = 'HMACSHA256=' . $signature;
            // dd($finalSignature);
            // dd($notificationHeader);
            if ($finalSignature == $notificationHeader['Signature']) {
                // TODO: Process if Signature is Valid
                dd('ok');
                // return response('OK', 200)->header('Content-Type', 'text/plain');
                
                // TODO: Do update the transaction status based on the `transaction.status`
            } else {
            
                dd('tolol');
            // TODO: Response with 400 errors for Invalid Signature
            // return response('Invalid Signature', 400)->header('Content-Type', 'text/plain');
        }

       
    }


}
