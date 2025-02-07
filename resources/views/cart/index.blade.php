@extends('layouts.main')

@section('content')
  <h1 class="text-3xl font-bold underline text-red-600 mb-6">Shopping Cart</h1>

  <p class="text-gray-700 mb-4">Welcome, <strong>{{ $userName }}</strong></p>

  @if (session('success'))
    <div class="bg-green-500 text-white px-4 py-2 rounded-md mb-4">
      {{ session('success') }}
    </div>
  @endif

  <div class="bg-white shadow-md rounded-lg p-6">
    @if (count($cartItems) > 0)
      <table class="w-full text-left">
        <thead>
          <tr class="border-b">
            <th class="p-2">Product</th>
            <th class="p-2">Quantity</th>
            <th class="p-2">Price</th>
            <th class="p-2">Total</th>
            <th class="p-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          @php $totalPrice = 0; @endphp
          @foreach ($cartItems as $id => $item)
            @php $itemTotal = $item['price'] * $item['quantity']; @endphp
            @php $totalPrice += $itemTotal; @endphp
            <tr class="border-b">
              <td class="p-2">{{ $item['name'] }}</td>
              <td class="p-2">
                <form action="{{ route('cart.update', $id) }}" method="POST" class="flex items-center">
                  @csrf
                  @method('PUT')
                  <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="w-16 text-center border-gray-300 rounded-md">
                  <button type="submit" class="bg-blue-500 text-white px-2 py-1 ml-2 rounded-md hover:bg-blue-700">
                    Update
                  </button>
                </form>
              </td>
              <td class="p-2">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
              <td class="p-2">Rp {{ number_format($itemTotal, 0, ',', '.') }}</td>
              <td class="p-2">
                <form action="{{ route('cart.remove', $id) }}" method="POST">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="bg-red-500 text-white px-4 py-1 rounded-md hover:bg-red-700">
                    Remove
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
          <tr>
            <td colspan="3" class="text-right font-bold p-2">Total Price:</td>
            <td class="p-2 font-bold">Rp {{ number_format($totalPrice, 0, ',', '.') }}</td>
            <td></td>
          </tr>
        </tbody>
      </table>

      <div class="flex justify-end mt-4">
        <a href="#" id="checkoutButton" onclick="payment()" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-700">
          Proceed to Checkout
        </a>
      </div>
    @else
      <p class="text-gray-600">Your cart is empty!</p>
    @endif
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://staging.doku.com/doku-js/assets/js/doku.js"></script>
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
  <script>
  function payment() {
    const cartItems = @json($cartItems);
    const totalPrice = {{ $totalPrice ?? 0}};
    const invoice = 'INV' + Math.floor(Math.random() * (100000 - 200000)) + 100000;

    // Kirim permintaan ke Laravel untuk memproses pembayaran
    fetch("{{ route('cart.checkout') }}", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": "{{ csrf_token() }}"
      },
      body: JSON.stringify({
        cart_items: cartItems,
        total_price: totalPrice,
        invoice_number: invoice,
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success && data.payment_url) {
          // Tampilkan popup DOKU
          loadJokulCheckout(data.payment_url);

          // Event listener untuk menangani callback dari DOKU
          window.addEventListener("message", function(event) {
              if (event.data && event.data.payment_status === "SUCCESS") {
                  updateTransactionStatus(invoice);
              }
          });
      } else {
        showAlert("Gagal memproses pembayaran. Silakan coba lagi.");
      }
    })
    .catch(error => {
      showAlert("Terjadi kesalahan saat memproses pembayaran.");
      console.error(error);
    });
}

// function updateTransactionStatus(invoice) {
//     fetch("{{ route('payment.callback') }}", {
//       method: "POST",
//       headers: {
//         "Content-Type": "application/json",
//         "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
//       },
//       body: JSON.stringify({
//         status: "SUCCESS",
//         invoice_number: invoice
//       })
//     })
//     .then(response => response.json())
//     .then(data => {
//       if (data.success) {
//         Swal.fire("Pembayaran Berhasil", "Transaksi Anda telah diperbarui.", "success");
//       } else {
//         Swal.fire("Error", "Terjadi kesalahan saat memperbarui transaksi.", "error");
//       }
//     })
//     .catch(error => {
//       Swal.fire("Error", "Terjadi kesalahan saat memperbarui transaksi.", "error");
//       console.error(error);
//     });
// }



  function showAlert(message) {
    Swal.fire({
      title: "Payment Error",
      text: message,
      icon: "error",
      button: "Close",
    });
  }

  </script>
  
  
  
@endsection
