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

  {{-- <script>
    $('#checkoutButton').on('click', function (e) {
      e.preventDefault();
  
      const amount = {{ $totalPrice }};
  
      $.ajax({
        url: '{{ route('checkout.process') }}',
        type: 'POST',
        headers: {
          'Client-Id': '{{ env('DOKU_CLIENT_ID') }}',
          'Request-Id': Date.now().toString(),
        },
        data: {
          amount: amount,
          _token: '{{ csrf_token() }}'
        },
        success: function (response) {
          if (response.payment_url) {
            window.open(response.payment_url, '_blank', 'width=500,height=600');
          }
        },
        error: function (xhr) {
          alert('Failed to process payment');
          console.error(xhr.responseText);
        }
      });
    });
  </script> --}}

  {{-- <script>
    $('#checkoutButton').on('click', function (e) {
      e.preventDefault();
  
      const amount = {{ $totalPrice }};
    
      $.ajax({
        url: '{{ route('checkout.process') }}',
        type: 'POST',
        data: {
          amount: amount,
          _token: '{{ csrf_token() }}'
        },
        success: function (response) {
          console.log(response);
          if (response.payment_data) {
            // Menampilkan modal pembayaran DOKU
            DokuPayment.init({
              paymentData: response.payment_data,
              onPaymentSuccess: function (result) {
                alert("Payment successful!");
                window.location.href = '{{ route('products.index') }}';
              },
              onPaymentError: function (error) {
                alert("Payment failed. Please try again.");
                console.error(error);
              }
            });
          }
        },
        error: function (xhr) {
          alert('Failed to process payment');
          console.error(xhr.responseText);
        }
      });
    });
  </script> --}}
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
  <script>
    function payment() {
      const requestTarget = "/checkout/v1/payment";
      const clientId = "{{ env('DOKU_CLIENT_ID') }}";
      const sharedKey = "{{ env('DOKU_SHARED_KEY') }}";
      const invoice = 'INV' + Math.floor(Math.random() * (100000 - 200000)) + 100000;
      const requestDate = new Date().toISOString().slice(0, 19) + "Z";
      const environmentUrl = 'https://sandbox.doku.com';
      
      // Prepare Body
      const body = prepareBody(invoice);
  
      // Create Header
      const headers = createHeader(body, clientId, sharedKey, invoice, requestDate, requestTarget);
  
      // AJAX Request
      fetch(environmentUrl + requestTarget, {
        method: 'POST',
        headers: headers,
        body: body,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.response && data.response.payment.url) {
            // Tampilkan modal pembayaran
            loadJokulCheckout(data.response.payment.url);
          } else {
            showAlert('Gagal memproses pembayaran. Silakan coba lagi.');
          }
        })
        .catch((error) => {
          showAlert('Terjadi kesalahan saat menghubungi server.');
          console.error(error);
        });
    }
  
    function prepareBody(invoice) {
      const customerName = "{{ $user->name ?? 'Customer' }}";
      const customerEmail = "{{ $user->email ?? 'email@customer.com' }}";
      const orderAmount = {{ $totalPrice }};
      const callbackUrl = "{{ route('checkout.callback') }}";
      
      return JSON.stringify({
        customer: {
          id: "{{ $user->id ?? '12345' }}",
          name: customerName,
          email: customerEmail,
          phone: "081234567890",
        },
        order: {
          amount: orderAmount,
          callback_url: callbackUrl,
          currency: "IDR",
          invoice_number: invoice,
        },
        payment: {
          payment_due_date: 60,
        },
      });
    }
  
    function createHeader(body, clientId, sharedKey, invoice, requestDate, requestTarget) {
      const digest = CryptoJS.enc.Base64.stringify(CryptoJS.SHA256(body));
      const rawSignature = [
        `Client-Id:${clientId}`,
        `Request-Id:${invoice}`,
        `Request-Timestamp:${requestDate}`,
        `Request-Target:${requestTarget}`,
        `Digest:${digest}`,
      ].join("\n");
  
      const signature = CryptoJS.enc.Base64.stringify(CryptoJS.HmacSHA256(rawSignature, sharedKey));
  
      return new Headers({
        "Content-Type": "application/json",
        "Signature": `HMACSHA256=${signature}`,
        "Request-Id": invoice,
        "Client-Id": clientId,
        "Request-Timestamp": requestDate,
      });
    }
  
    function showAlert(message) {
      swal({
        title: "Payment Error",
        text: message,
        icon: "error",
        button: "Close",
      });
    }
  </script>
  
  
  
@endsection
