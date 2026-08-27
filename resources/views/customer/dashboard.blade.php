<x-layouts.app title="My Dashboard | ServEase">
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Welcome Header -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex flex-col sm:flex-row items-center justify-between">
                <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                    <img class="h-16 w-16 rounded-full object-cover border-2 border-amber-100" src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name) }}" alt="Avatar">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h2>
                        <p class="mt-1 text-sm text-gray-500">Manage your service requests and active bookings here.</p>
                    </div>
                </div>
                <a href="/#services" class="px-5 py-2.5 bg-amber-600 text-white rounded-md text-sm font-semibold hover:bg-amber-700 transition shadow-sm">
                    Book a Service
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Service Requests -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Requests</h3>
                    </div>
                    <div class="p-6">
                        @forelse($serviceRequests as $request)
                            <div class="mb-4 pb-4 border-b border-gray-100 last:mb-0 last:pb-0 last:border-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                       <h4 class="font-semibold text-amber-600 hover:text-amber-800">
                                            <a href="{{ route('service-requests.show', $request->id) }}">
                                                {{ $request->service->name ?? 'Unknown Service' }} &rarr;
                                            </a>
                                        </h4>
                                        <p class="text-xs text-gray-500 mt-1">Requested on: {{ $request->created_at->format('d M, Y') }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize 
                                        {{ ($request->status?->value ?? $request->status) === 'draft' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ str_replace('_', ' ', $request->status?->value ?? $request->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <p class="text-sm text-gray-500">You haven't requested any services yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Active Bookings</h3>
                    </div>
                    <div class="p-6">
                        @forelse($bookings as $booking)
                            <div class="mb-4 pb-4 border-b border-gray-100 last:mb-0 last:pb-0 last:border-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                       <h4 class="font-semibold text-emerald-600 hover:text-emerald-800">
                                            <a href="{{ route('bookings.show', $booking->id) }}">
                                                {{ $booking->service->name ?? 'Unknown Service' }} &rarr;
                                            </a>
                                        </h4>
                                        <p class="text-xs text-gray-500 mt-1">Provider: <span class="font-medium text-gray-700">{{ $booking->provider->name ?? 'Pending Allocation' }}</span></p>
                                        <p class="text-xs text-gray-500 mt-1">Scheduled: {{ $booking->scheduled_at ? \Carbon\Carbon::parse($booking->scheduled_at)->format('d M, Y h:i A') : 'TBD' }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize bg-emerald-100 text-emerald-800">
                                        {{ str_replace('_', ' ', $booking->status?->value ?? $booking->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <p class="text-sm text-gray-500">You have no active bookings right now.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>