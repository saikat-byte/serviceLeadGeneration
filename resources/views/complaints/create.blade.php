<x-layouts.app>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
        
        <div class="bg-white rounded-lg shadow-sm border border-red-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-red-100 bg-red-50">
                <h2 class="text-xl font-bold text-red-800">File a Complaint</h2>
                <p class="text-sm text-red-600 mt-1">For Booking #{{ $booking->id }} - {{ $booking->serviceRequest->service->name ?? 'Service' }}</p>
            </div>

            <div class="p-6">
                @php
                    $rolePrefix = (auth()->user()->role instanceof \BackedEnum ? auth()->user()->role->value : auth()->user()->role) === 'provider' ? 'provider.' : 'customer.';
                @endphp
                <form action="{{ route($rolePrefix . 'complaints.store', $booking) }}" method="POST">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Complaint Type</label>
                            <select name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                <option value="">Select an issue...</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->value }}">{{ ucwords(str_replace('_', ' ', $type->value)) }}</option>
                                @endforeach
                            </select>
                            @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Detailed Description</label>
                            <textarea name="description" rows="5" required placeholder="Please explain the issue in detail..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"></textarea>
                            @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent bg-red-600 py-2 px-6 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none transition">
                            Submit Complaint
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>