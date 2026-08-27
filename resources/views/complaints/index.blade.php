<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">My Complaints & Support Tickets</h1>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            @if($complaints->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($complaints as $complaint)
                        <li class="p-6 hover:bg-gray-50 transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-medium text-red-600">
                                        {{ ucwords(str_replace('_', ' ', $complaint->type->value ?? $complaint->type)) }}
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Against: <span class="font-semibold">{{ $complaint->againstUser->name ?? 'Unknown' }}</span> | 
                                        Booking #{{ $complaint->booking_id }}
                                    </p>
                                    <p class="text-sm text-gray-700 mt-2 bg-gray-100 p-3 rounded-md">{{ $complaint->description }}</p>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium uppercase tracking-wider
                                        {{ $complaint->status->value === 'created' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ str_replace('_', ' ', $complaint->status->value ?? $complaint->status) }}
                                    </span>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="p-10 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No Complaints</h3>
                    <p class="mt-1 text-sm text-gray-500">You haven't filed any complaints yet.</p>
                </div>
            @endif
        </div>

    </div>
</x-layouts.app>