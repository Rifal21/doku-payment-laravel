<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItems;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = session()->get('cart', []);
        $userName = auth()->user()->name;
    
        return view('cart.index', compact('cartItems', 'userName'));
    }
    

    public function add($productId)
    {
        $product = Product::findOrFail($productId);
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                "product_id" => $productId,
                "name" => $product->name,
                "price" => $product->price,
                "quantity" => 1,
            ];
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Product added to cart!');
    }
    public function update(Request $request, $id)
{
    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {
        $cart[$id]['quantity'] = $request->quantity;
        session()->put('cart', $cart);
    }

    return redirect()->route('cart.index')->with('success', 'Cart updated successfully!');
}

public function remove($id)
{
    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {
        unset($cart[$id]);
        session()->put('cart', $cart);
    }

    return redirect()->route('cart.index')->with('success', 'Product removed from cart!');
}

// public function storeTransaction(Request $request)
// {
//     $validated = $request->validate([
//         'cart_items' => 'required|array',
//         'total_price' => 'required|numeric',
//         'invoice' => 'required|string',
//     ]);

//     // Simpan transaksi ke database
//     $transaction = Transaction::create([
//         'invoice_number' => $validated['invoice'],
//         'user_id' => auth()->id(),
//         'cart_items' => json_encode($validated['cart_items']),
//         'total_price' => $validated['total_price'],
//         'status' => 'pending',
//     ]);

//     // Simpan item yang dibeli
//     foreach ($validated['cart_items'] as $item) {
//         TransactionItems::create([
//             'transaction_id' => $transaction->id,
//             'product_id' => $item['id'],
//             'quantity' => $item['quantity'],
//             'price' => $item['price'],
//         ]);
//     }
    
//     // dd($transaction->id, $validated['cart_items']);

//     return response()->json(['success' => true, 'message' => 'Transaction saved successfully.']);
// }

    public function applyCoupon(Request $request)
    {

        $coupon = Coupon::where('code', $request->coupon_code)->first();
        // dd($coupon);

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Invalid coupon code']);
        }

        $cartItems = session()->get('cart', []);
        // dd($cartItems);
        $total = 0;

        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $discount = ($total * $coupon->discount_percentage) / 100;
        $newTotal = $total - $discount;

        // dd($discount, $newTotal);

        return response()->json([
            'success' => true,
            'message' => "Coupon applied! You saved Rp " . number_format($discount, 0, ',', '.'),
            'newTotal' => $newTotal
        ]); 
    }


}
