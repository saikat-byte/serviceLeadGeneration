<x-layouts.app>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 mb-20">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Booking #{{ $booking->id }}</h1>
                <p class="text-gray-500 mt-1">Placed on {{ $booking->created_at->format('d M, Y') }}</p>
            </div>
            <span
                class="px-4 py-2 rounded-full text-xs md:text-sm font-bold bg-amber-100 text-amber-800 uppercase tracking-wide border border-amber-200">
                {{ str_replace('_', ' ', $booking->status->value ?? $booking->status) }}
            </span>
        </div>

        <!-- Content Grid -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- Service Details -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                        Service Details
                    </h2>
                    <div class="space-y-3">
                        <p class="text-gray-600"><span
                                class="font-medium text-gray-900 w-24 inline-block">Service:</span>
                            {{ $booking->serviceRequest->service->name ?? 'N/A' }}</p>
                        <p class="text-gray-600"><span
                                class="font-medium text-gray-900 w-24 inline-block">Location:</span>
                            {{ $booking->serviceRequest->location ?? 'N/A' }}</p>
                        <p class="text-gray-600"><span
                                class="font-medium text-gray-900 w-24 inline-block">Details:</span>
                            {{ $booking->serviceRequest->requirements ?? 'No extra details provided.' }}</p>
                    </div>
                </div>

                <!-- User Details -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        @if(auth()->id() === $booking->customer_id)
                            Provider Information
                        @else
                            Customer Information
                        @endif
                    </h2>

                    @php
                        $targetUser = auth()->id() === $booking->customer_id ? $booking->provider : $booking->customer;
                    @endphp

                    @if($targetUser)
                        <div class="flex items-center space-x-4 bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <img class="h-16 w-16 rounded-full object-cover border-2 border-white shadow-sm"
                                src="{{ $targetUser->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($targetUser->name) }}"
                                alt="{{ $targetUser->name }}">
                            <div>
                                <p class="font-bold text-gray-900 text-lg">{{ $targetUser->name }}</p>
                                <p class="text-sm text-gray-600 flex items-center mt-1">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                        </path>
                                    </svg>
                                    {{ $targetUser->mobile ?? 'No phone number' }}
                                </p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 italic">User information is not available.</p>
                    @endif
                </div>

            </div>
        </div>

        <!-- Support & Actions Section -->
        <div
            class="bg-gray-50 p-6 rounded-lg border border-gray-200 flex flex-col md:flex-row gap-4 items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-800">Need help with this booking?</h3>
                <p class="text-sm text-gray-500">If you are facing any issues like delay or bad behavior, please let us
                    know.</p>
            </div>
            <div class="flex-shrink-0">
                @php
                    $rolePrefix = (auth()->user()->role instanceof \BackedEnum ? auth()->user()->role->value : auth()->user()->role) === 'provider' ? 'provider.' : 'customer.';
                @endphp
                <!-- COMPLAINT BUTTON ADDED HERE -->
                <a href="{{ route($rolePrefix . 'complaints.create', $booking->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-red-50 border border-red-200 text-red-700 rounded-md text-sm font-medium hover:bg-red-100 hover:border-red-300 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    Report an Issue
                </a>
            </div>
        </div>

    </div>
</x-layouts.app>