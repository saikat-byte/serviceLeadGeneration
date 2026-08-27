<x-layouts.app title="Add Location | ServEase">
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-200">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Add New Address</h2>
                    <p class="text-sm text-gray-500 mt-1">Please provide the address where you need the service.</p>
                </div>

                <form action="{{ route('locations.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="redirect" value="{{ $redirect }}">

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Address Label (e.g., Home, Office) <span class="text-red-500">*</span></label>
                        <input type="text" name="label" required placeholder="Home" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                        @error('label') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Full Address <span class="text-red-500">*</span></label>
                        <textarea name="address" rows="3" required placeholder="House/Flat No, Street Name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm"></textarea>
                        @error('address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Locality / Area <span class="text-red-500">*</span></label>
                            <input type="text" name="locality" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                            @error('locality') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">City <span class="text-red-500">*</span></label>
                            <input type="text" name="city" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                            @error('city') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Postal Code / PIN <span class="text-red-500">*</span></label>
                        <input type="text" name="postal_code" required class="mt-1 block w-full sm:w-1/2 border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                        @error('postal_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 border-t border-gray-200 flex justify-end space-x-3">
                        <a href="{{ $redirect }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                            Save Address
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>