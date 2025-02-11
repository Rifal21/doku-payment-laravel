<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ isset($coupon) ? __('Edit Coupon') : __('Create Coupon') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ isset($coupon) ? route('coupon.update', $coupon->id) : route('coupon.store') }}" method="POST">
                        @csrf
                        @if(isset($coupon))
                            @method('PUT')
                        @endif
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300">Coupon Code</label>
                            <input type="text" name="code" value="{{ old('code', $coupon->code ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300">Discount Percentage</label>
                            <input type="number" name="discount_percentage" value="{{ old('discount_percentage', $coupon->discount_percentage ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" min="1" max="100" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300">Max Uses</label>
                            <input type="number" name="max_uses" value="{{ old('max_uses', $coupon->max_uses ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300">Expiration Date</label>
                            <input type="datetime-local" name="expires_at" 
                            value="{{ old('expires_at', isset($coupon) && $coupon->expires_at ? \Carbon\Carbon::parse($coupon->expires_at)->format('Y-m-d\TH:i') : '') }}" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        

                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300">Status</label>
                            <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="tersedia" {{ (old('status', $coupon->status ?? '') == 'tersedia') ? 'selected' : '' }}>Tersedia</option>
                                <option value="tidak tersedia" {{ (old('status', $coupon->status ?? '') == 'tidak tersedia') ? 'selected' : '' }}>Tidak Tersedia</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-300">Active</span>
                            </label>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                {{ isset($coupon) ? 'Update Coupon' : 'Create Coupon' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
