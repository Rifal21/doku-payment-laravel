@extends('layouts.main')

@section('content')
  <h1 class="text-3xl font-bold underline text-red-600 mb-6">Checkout</h1>

  <table class="w-full text-left">
    <thead>
      <tr class="border-b">
        <th class="p-2">Product</th>
        <th class="p-2">Quantity</th>
        <th class="p-2">Price</th>
        <th class="p-2">Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($cartItems as $item)
        <tr class="border-b">
          <td class="p-2">{{ $item['name'] }}</td>
          <td class="p-2">{{ $item['quantity'] }}</td>
          <td class="p-2">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
          <td class="p-2">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
        </tr>
      @endforeach
      <tr>
        <td colspan="3" class="text-right font-bold p-2">Total Price:</td>
        <td class="p-2 font-bold">Rp {{ number_format($totalPrice, 0, ',', '.') }}</td>
      </tr>
    </tbody>
  </table>

  <div class="flex justify-end mt-4">
    <form action="{{ route('transaction.pay') }}" method="POST">
      @csrf
      <input type="hidden" name="total_price" value="{{ $totalPrice }}">
      <button type="submit" id="checkout-button" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-700">
        Proceed to Payment
      </button>
    </form>
  </div>
  <script type="text/javascript">
    var checkoutButton = document.getElementById('checkout-button');
    // Example: the payment page will show when the button is clicked
    checkoutButton.addEventListener('click', function () {
        loadJokulCheckout('https://jokul.doku.com/checkout/link/SU5WFDferd561dfasfasdfae123c20200510090550775'); // Replace it with the response.payment.url you retrieved from the response
    });
    </script>
@endsection
