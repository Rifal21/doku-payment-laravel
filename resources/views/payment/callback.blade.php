@extends('layouts.main')

@section('content')
<div class="min-h-screen min-w-full flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded-2xl shadow-lg max-w-md text-center">
        <div class="flex justify-center">
            <svg class="w-20 h-20 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 mt-4">Payment Successful!</h2>
        <p class="text-gray-600 mt-2">Thank you for your purchase. Your payment has been successfully processed.</p>
        <a href="{{ route('products.index') }}" class="mt-6 inline-block bg-green-500 text-white px-6 py-3 rounded-lg shadow-md hover:bg-green-600 transition">
            Go to Homepage
        </a>
    </div>
</div>
@endsection
