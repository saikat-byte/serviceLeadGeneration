<x-layouts.app title="Booking Details | ServEase">
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="flex items-center justify-between mb-4">
                <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Dashboard
                </a>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium capitalize bg-emerald-100 text-emerald-800">
                    Status: {{ str_replace('_', ' ', $booking->status?->value ?? $booking->status) }}
                </span>
            </div>

            <!-- Booking Summary -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $booking->service->name ?? 'Service Booking' }}</h2>
                        <p class="text-gray-500 text-sm">Booking ID: #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-gray-900">₹{{ number_format($booking->final_amount ?? $booking->estimated_amount, 2) }}</p>
                        <p class="text-sm text-gray-500">Estimated Cost</p>
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Service Provider</h4>
                        <div class="flex items-center">
                            <img class="h-10 w-10 rounded-full object-cover border border-gray-300" src="{{ $booking->provider->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($booking->provider->name) }}" alt="Provider">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $booking->provider->name }}</p>
                                <p class="text-xs text-gray-500">Professional</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Schedule & Location</h4>
                        <p class="text-sm text-gray-900 mb-1"><strong class="font-medium">Date:</strong> {{ $booking->scheduled_at ? \Carbon\Carbon::parse($booking->scheduled_at)->format('d M, Y h:i A') : 'TBD' }}</p>
                        <p class="text-sm text-gray-900"><strong class="font-medium">Location:</strong> {{ $booking->connection->lead->serviceRequest->location->address ?? 'Address not found' }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment & Review Actions -->
            <div class="grid grid-cols-1 gap-6">
                
                <!-- Payment Section -->
                @if(in_array($booking->status?->value ?? $booking->status, ['payment_pending', 'closed', 'work_completed']))
                    @if(!$booking->payment || ($booking->payment->status?->value ?? $booking->payment->status) !== 'paid')
                        <div class="bg-amber-50 p-6 rounded-lg border border-amber-200 flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-amber-900">Payment Required</h3>
                                <p class="text-sm text-amber-700">The service has been completed. Please proceed to payment.</p>
                            </div>
                            <form action="{{ route('bookings.pay', $booking->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-6 py-2.5 bg-amber-600 text-white rounded-md text-sm font-bold shadow-sm hover:bg-amber-700 transition">
                                    Pay ₹{{ number_format($booking->final_amount ?? $booking->estimated_amount, 2) }}
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-emerald-50 p-6 rounded-lg border border-emerald-200">
                            <div class="flex items-center text-emerald-800 font-bold mb-1">
                                <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                Payment Completed
                            </div>
                            <p class="text-sm text-emerald-700">Paid ₹{{ number_format($booking->payment->amount, 2) }} successfully.</p>
                        </div>
                    @endif
                @endif

                <!-- Review Section -->
                @if(($booking->status?->value ?? $booking->status) === 'closed' || ($booking->payment && ($booking->payment->status?->value ?? $booking->payment->status) === 'paid'))
                    @if(!$booking->review)
                        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Rate Your Experience</h3>
                            <form action="{{ route('bookings.review', $booking->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Rating (1-5)</label>
                                    <select name="rating" required class="mt-1 block w-32 border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                        <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                                        <option value="4">⭐⭐⭐⭐ (4/5)</option>
                                        <option value="3">⭐⭐⭐ (3/5)</option>
                                        <option value="2">⭐⭐ (2/5)</option>
                                        <option value="1">⭐ (1/5)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Write a Review</label>
                                    <textarea name="comment" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm" placeholder="How was the service?"></textarea>
                                </div>
                                <button type="submit" class="px-5 py-2 bg-gray-900 text-white rounded-md text-sm font-bold shadow-sm hover:bg-gray-800 transition">
                                    Submit Review
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Your Review</h3>
                            <div class="flex text-yellow-400 mb-2">
                                @for($i = 0; $i < $booking->review->rating; $i++)
                                    <svg class="h-5 w-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            <p class="text-sm text-gray-600 italic">"{{ $booking->review->comment }}"</p>
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-layouts.app>