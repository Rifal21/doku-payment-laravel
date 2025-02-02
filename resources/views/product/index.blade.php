@extends('layouts.main')

@section('content')
  {{-- <h1 class="text-3xl font-bold underline text-red-600 mb-6">
    Product Index
  </h1> --}}

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-5">
    @foreach ($products as $product)
      <div class="bg-white shadow-lg rounded-2xl overflow-hidden hover:scale-105 transform transition duration-300">
        <div class="relative">
          @if ($product->image)
            <img src="{{$product->image}}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
          @else
            <div class="bg-gray-200 w-full h-48 flex items-center justify-center text-gray-500">
              No Image Available
            </div>
          @endif
        </div>
        <div class="p-4">
          <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $product->name }}</h2>
          <p class="text-gray-600 text-sm mb-4">
            {{ Str::limit($product->description, 80) }}
          </p>
          <div class="flex justify-between items-center">
            <span class="text-xl font-bold text-green-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
            <form action="{{ route('cart.add', $product->id) }}" method="POST">
              @csrf
              <button class="bg-blue-500 text-white px-4 py-2 rounded-xl hover:bg-blue-700 transition flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M16 11V9a4 4 0 10-8 0v2a1 1 0 01-.293.707L6 13h8l-1.707-1.293A1 1 0 0114 11zm-4 4a2 2 0 100-4 2 2 0 000 4z" />
                </svg>
                Add to Cart
              </button>
            </form>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endsection
