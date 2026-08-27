<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        
        <!-- Provider Header Card -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8 border-t-4 border-emerald-500">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center space-x-4">
                    <img class="h-16 w-16 rounded-full object-cover border-2 border-emerald-100" 
                         src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name) }}" 
                         alt="{{ auth()->user()->name }}">
                    <div>
                        <div class="flex items-center space-x-2">
                            <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h1>
                            <span class="bg-emerald-100 text-emerald-800 text-xs px-2 py-1 rounded-full font-bold uppercase tracking-wide">
                                Provider Panel
                            </span>
                        </div>
                        <p class="text-gray-500 text-sm mt-1">Review new leads and manage your active jobs.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Available Leads Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-lg font-semibold text-gray-800">New Leads (Matched for you)</h2>
                </div>
                <div class="p-6">
                    @if(isset($leads) && $leads->count() > 0)
                        <div class="space-y-4">
                            @foreach($leads as $lead)
                                <div class="border rounded-md p-4 hover:border-emerald-300 transition">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-medium text-emerald-700">{{ $lead->serviceRequest->service->name ?? 'Service' }}</h3>
                                            <p class="text-xs text-gray-500 mt-1">Location: {{ $lead->serviceRequest->location ?? 'N/A' }}</p>
                                        </div>
                                        <form action="{{ route('provider.leads.interest', $lead) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xs bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded">
                                                Accept Job
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center py-4">No new leads available right now.</p>
                    @endif
                </div>
            </div>

            <!-- Active Jobs Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-lg font-semibold text-gray-800">Your Active Jobs</h2>
                </div>
                <div class="p-6">
                    @if(isset($activeJobs) && $activeJobs->count() > 0)
                        <div class="space-y-4">
                            @foreach($activeJobs as $job)
                                <div class="border rounded-md p-4">
                                    <h3 class="font-medium text-gray-800">Job #{{ $job->id }}</h3>
                                    <p class="text-xs text-gray-500 mt-1">Status: <span class="font-semibold text-amber-600">{{ $job->status }}</span></p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center py-4">You have no active jobs at the moment.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>