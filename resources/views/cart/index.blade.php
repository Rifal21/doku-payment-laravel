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
                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1"
                            class="w-16 text-center border-gray-300 rounded-md">
                        <button type="submit"
                            class="bg-blue-500 text-white px-2 py-1 ml-2 rounded-md hover:bg-blue-700">
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
                        <button type="submit"
                            class="bg-red-500 text-white px-4 py-1 rounded-md hover:bg-red-700">
                            Remove
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3" class="text-right font-bold p-2">Total Price:</td>
                <td class="p-2 font-bold">Rp <span id="total-price">{{ number_format($totalPrice, 0, ',', '.') }}</span></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    {{-- Form untuk kode kupon --}}
    <div class="mt-4 p-4 bg-gray-100 rounded-lg w-full">
        <form id="couponForm" method="POST" action="{{ route('cart.applyCoupon') }}" class="flex items-center justify-end gap-2">
            @csrf
            <input type="text" name="coupon_code" id="couponCode" placeholder="Enter Coupon Code"
                class="border-gray-300 rounded-md px-3 py-2 w-full">
            <button type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-700 ml-3">
                Apply Coupon
            </button>
        </form>
        <p id="couponMessage" class="text-green-600 mt-2 hidden"></p>
    </div>

    <div class="flex justify-end mt-4">
        <a href="#" id="checkoutButton" onclick="payment()"
            class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-700">
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
        const invoice = 'INV' + (Math.floor(Math.random() * 100000) + 100000); 

        const orderItems = Object.keys(cartItems).map(id => ({
            product_id: id,
            quantity: cartItems[id].quantity
        }));

        fetch("{{ route('payment.headers') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    invoice_number: invoice,
                    amount: parseInt($("#total-price").text().replace(/\./g, '')) || {{ $totalPrice ?? 0 }},
                    items: orderItems , 
                    coupon_code: $('#couponCode').val().toUpperCase()
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (!data.headers || !data.body) {
                    showAlert("Gagal mendapatkan header pembayaran.");
                    return;
                }
                fetch("https://api-sandbox.doku.com" + data.request_target, {
                        method: "POST",
                        headers: data.headers,
                        body: JSON.stringify(data.body)
                    })
                    .then(response => response.json())
                    .then(responseData => {
                        // console.log(responseData);
                        if (responseData.response && responseData.response.payment && responseData.response.payment.url) {
                            window.location.href = responseData.response.payment.url;
                        } else {
                            showAlert("Gagal memproses pembayaran.");
                        }
                    })
                    .catch(error => {
                        console.log(error);
                        showAlert("Terjadi kesalahan saat menghubungi DOKU.");
                    });
            })
            .catch(error => {
                showAlert("Gagal mendapatkan signature.");
            });
    }

    function showAlert(message) {
        Swal.fire({
            title: "Payment Error",
            text: message,
            icon: "error",
            confirmButtonText: "Close",
        });
    }

    // Handle kupon
    $("#couponForm").submit(function (event) {
        event.preventDefault();
        const couponCode = $("#couponCode").val();

        $.ajax({
            url: "{{ route('cart.applyCoupon') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                coupon_code: couponCode,
                cart_items: @json($cartItems),
            },
            success: function (response) {
                if (response.success) {
                    // console.log(response.newTotal);
                    $("#couponMessage").removeClass("hidden").text(response.message);
                    $("#total-price").text(parseFloat(response.newTotal).toLocaleString('id-ID'));
                } else {
                    showAlert(response.message);
                }
            },
            error: function () {
                showAlert("Terjadi kesalahan saat menerapkan kupon.");
            }
        });
    });
</script>
@endsection
