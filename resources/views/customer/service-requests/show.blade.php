<x-layouts.app title="Request Details | ServEase">
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="flex items-center justify-between mb-4">
                <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Dashboard
                </a>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium capitalize 
                    {{ ($serviceRequest->status?->value ?? $serviceRequest->status) === 'draft' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800' }}">
                    Status: {{ str_replace('_', ' ', $serviceRequest->status?->value ?? $serviceRequest->status) }}
                </span>
            </div>

            <!-- Request Summary -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $serviceRequest->service->name ?? 'Service Request' }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-600">
                    <div>
                        <p class="mb-2"><strong class="text-gray-900">Location:</strong> {{ $serviceRequest->location->locality ?? 'N/A' }}, {{ $serviceRequest->location->city ?? 'N/A' }}</p>
                        <p class="mb-2"><strong class="text-gray-900">Preferred Date:</strong> {{ $serviceRequest->preferred_at ? \Carbon\Carbon::parse($serviceRequest->preferred_at)->format('d M, Y') : 'Flexible' }}</p>
                        <p><strong class="text-gray-900">Urgency:</strong> <span class="uppercase">{{ $serviceRequest->urgency }}</span></p>
                    </div>
                    <div>
                        <p class="mb-2"><strong class="text-gray-900">Requested On:</strong> {{ $serviceRequest->created_at->format('d M, Y h:i A') }}</p>
                        <p class="mb-2"><strong class="text-gray-900">Description:</strong></p>
                        <p class="bg-gray-50 p-3 rounded border border-gray-100">{{ $serviceRequest->description ?? 'No additional description provided.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Interested Providers List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800">Available Professionals</h3>
                    <p class="text-sm text-gray-500">Review the professionals who are ready to take your job and book the best fit.</p>
                </div>
                <div class="p-6">
                    @if($serviceRequest->lead && $serviceRequest->lead->interests->count() > 0)
                        <div class="space-y-6">
                            @foreach($serviceRequest->lead->interests as $interest)
                                <div class="flex flex-col sm:flex-row items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-amber-300 transition">
                                    <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                                        <img class="h-12 w-12 rounded-full object-cover border border-gray-300" src="{{ $interest->provider->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($interest->provider->name) }}" alt="Provider">
                                        <div>
                                            <h4 class="font-bold text-gray-900">{{ $interest->provider->name }}</h4>
                                            <div class="flex items-center text-sm text-gray-500 mt-1">
                                                <svg class="h-4 w-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                {{ $interest->provider->providerProfile->rating_average ?? 'New' }} 
                                                <span class="mx-2">•</span> 
                                                {{ $interest->provider->providerProfile->completed_jobs_count ?? 0 }} jobs done
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <!-- Note: Only allow booking if request is not already fulfilled -->
                                        @if(in_array($serviceRequest->lead->status?->value ?? $serviceRequest->lead->status, ['selected', 'converted']))
                                            <button disabled class="px-5 py-2 bg-gray-300 text-gray-600 rounded-md text-sm font-semibold cursor-not-allowed">
                                                Job Closed
                                            </button>
                                        @else
                                            <form action="{{ route('service-requests.book', ['serviceRequest' => $serviceRequest->id, 'provider' => $interest->provider->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Are you sure you want to book {{ $interest->provider->name }}?')" class="px-5 py-2 bg-amber-600 text-white rounded-md text-sm font-bold shadow-sm hover:bg-amber-700 transition">
                                                    Book Now
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50 rounded-md border border-dashed border-gray-300">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-gray-600 font-medium">Waiting for Professionals</p>
                            <p class="text-gray-500 text-sm mt-1">We've sent your request to local experts. Responses will appear here soon.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>