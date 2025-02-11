<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manage Coupons') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <a href="/dashboard/coupon/create" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-700">Create Coupon</a>
                    <div class="flex flex-col mt-4">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount (%)</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Uses</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Used Count</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($coupon as $coupon)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">{{ $coupon->code }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap">{{ $coupon->discount_percentage }}%</td>
                                                    <td class="px-6 py-4 whitespace-nowrap">{{ $coupon->max_uses ?? 'Unlimited' }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap">{{ $coupon->used_count }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap">{{ $coupon->status }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <a href="{{ route('coupon.update', $coupon->id . '/edit') }}" class="text-blue-500 hover:text-blue-700">Edit</a>
                                                        <form action="{{ route('coupon.destroy', $coupon->id) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure?')">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>