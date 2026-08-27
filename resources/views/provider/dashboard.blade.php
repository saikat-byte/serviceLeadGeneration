<x-layouts.app title="Provider Dashboard | ServEase">
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Welcome Header -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex items-center space-x-4">
                <img class="h-16 w-16 rounded-full object-cover border-2 border-indigo-100" src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name) }}" alt="Avatar">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Provider Portal</h2>
                    <p class="mt-1 text-sm text-gray-500">Welcome back, {{ auth()->user()->name }}. Manage your jobs and leads here.</p>
                </div>
            </div>

            <!-- Active Jobs Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-indigo-50 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-indigo-900">My Active Jobs</h3>
                </div>
                <div class="p-6">
                    @forelse($activeJobs as $job)
                        <div class="mb-6 pb-6 border-b border-gray-100 last:mb-0 last:pb-0 last:border-0">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                                <div class="mb-4 sm:mb-0">
                                    <div class="flex items-center space-x-3 mb-1">
                                        <h4 class="text-lg font-bold text-gray-900">{{ $job->booking->service->name ?? 'Service Job' }}</h4>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize bg-indigo-100 text-indigo-800">
                                            {{ str_replace('_', ' ', $job->status?->value ?? $job->status) }}
                                        </span>
                                    </div>
                                    <div class="mt-2 space-y-1 text-sm text-gray-600">
                                        <p class="flex items-center">
                                            <strong class="mr-2">Customer:</strong> {{ $job->booking->customer->name ?? 'Unknown' }}
                                        </p>
                                        <p class="flex items-center">
                                            <strong class="mr-2">Location:</strong> {{ $job->booking->connection->lead->serviceRequest->location->address ?? 'N/A' }}, {{ $job->booking->connection->lead->serviceRequest->location->locality ?? '' }}
                                        </p>
                                        <p class="flex items-center">
                                            <strong class="mr-2">Scheduled:</strong> {{ $job->booking->scheduled_at ? \Carbon\Carbon::parse($job->booking->scheduled_at)->format('d M, Y h:i A') : 'TBD' }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="w-full sm:w-auto flex space-x-2">
                                    @if(($job->status?->value ?? $job->status) === 'created' || ($job->status?->value ?? $job->status) === 'scheduled')
                                        <form action="{{ route('provider.jobs.start', $job->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 text-white rounded-md text-sm font-bold shadow-sm hover:bg-blue-700 transition">
                                                Start Job
                                            </button>
                                        </form>
                                    @elseif(($job->status?->value ?? $job->status) === 'started')
                                        <form action="{{ route('provider.jobs.complete', $job->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-emerald-600 text-white rounded-md text-sm font-bold shadow-sm hover:bg-emerald-700 transition">
                                                Complete Job
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6">
                            <p class="text-gray-500 font-medium">No active jobs right now.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Leads Section (Existing logic) -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">Available Job Leads</h3>
                </div>
                <div class="p-6">
                    @forelse($leads as $lead)
                        <div class="mb-6 pb-6 border-b border-gray-100 last:mb-0 last:pb-0 last:border-0">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                                <div class="mb-4 sm:mb-0">
                                    <h4 class="text-lg font-bold text-gray-900">{{ $lead->serviceRequest->service->name ?? 'Service Required' }}</h4>
                                    <div class="mt-2 space-y-1 text-sm text-gray-600">
                                        <p class="flex items-center">
                                            <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                            {{ $lead->serviceRequest->location->locality ?? 'Unknown Location' }}, {{ $lead->serviceRequest->location->city ?? '' }}
                                        </p>
                                        <p class="flex items-center">
                                            <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            Preferred Date: {{ $lead->serviceRequest->preferred_at ? \Carbon\Carbon::parse($lead->serviceRequest->preferred_at)->format('d M, Y') : 'Flexible' }}
                                        </p>
                                        <p class="flex items-center">
                                            <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            Urgency: <span class="ml-1 font-medium uppercase text-xs px-2 py-0.5 rounded-full {{ $lead->serviceRequest->urgency === 'normal' ? 'bg-gray-100 text-gray-700' : 'bg-red-100 text-red-700' }}">{{ $lead->serviceRequest->urgency }}</span>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="w-full sm:w-auto">
                                    @php
                                        $hasExpressedInterest = \App\Models\Interest::where('lead_id', $lead->id)
                                                                    ->where('provider_id', auth()->id())
                                                                    ->exists();
                                    @endphp

                                    @if($hasExpressedInterest)
                                        <button disabled class="w-full sm:w-auto px-4 py-2 bg-gray-300 text-gray-600 rounded-md text-sm font-semibold cursor-not-allowed">
                                            Interest Sent
                                        </button>
                                    @else
                                        <form action="{{ route('provider.leads.interest', $lead->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 text-white rounded-md text-sm font-bold shadow-sm hover:bg-indigo-700 transition">
                                                Accept Job
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <p class="text-gray-500 font-medium">No New Leads Available</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>