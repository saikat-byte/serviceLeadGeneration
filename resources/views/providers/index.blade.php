<x-layouts.app title="Trusted Professionals | ServEase">
    <div class="bg-gray-50 py-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <x-breadcrumbs :links="[
                'Professionals' => null
            ]" />

            <div class="mb-10 text-center sm:text-left">
                <h1 class="text-3xl font-extrabold text-gray-900">Discover Professionals</h1>
                <p class="mt-2 text-gray-500 max-w-2xl">Find highly rated, verified local service providers ready to help you.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($providers as $provider)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-amber-300 transition-all duration-200 flex flex-col group overflow-hidden">
                        
                        <!-- Top Banner / Avatar -->
                        <div class="relative h-24 bg-gradient-to-r from-amber-500 to-amber-600"></div>
                        <div class="px-6 relative flex justify-center -mt-12">
                            @if(optional($provider)->avatar)
                                <img src="{{ $provider->avatar }}" alt="{{ $provider->name }}" class="h-24 w-24 rounded-full border-4 border-white object-cover bg-white shadow-sm">
                            @else
                                <div class="h-24 w-24 rounded-full border-4 border-white bg-gray-100 flex items-center justify-center text-gray-400 text-3xl font-bold shadow-sm">
                                    {{ substr($provider->name, 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="p-6 flex-grow text-center">
                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-amber-600 transition-colors">
                                <a href="{{ route('providers.show', $provider->id) }}" class="focus:outline-none">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    {{ $provider->name }}
                                </a>
                            </h3>
                            
                            <!-- Stats -->
                            <div class="flex items-center justify-center mt-3 gap-4">
                                <div class="flex items-center text-sm font-semibold text-gray-700">
                                    <svg class="w-4 h-4 text-amber-400 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    {{ number_format(optional($provider->providerProfile)->rating_average ?? 0, 1) }}
                                </div>
                                <div class="text-gray-300">|</div>
                                <div class="text-sm text-gray-600">
                                    {{ optional($provider->providerProfile)->completed_jobs_count ?? 0 }} Jobs
                                </div>
                            </div>

                            <!-- Services List Snippet -->
                            @if($provider->providerServices->count() > 0)
                                <p class="text-xs text-gray-500 mt-4 line-clamp-1">
                                    Expert in: {{ $provider->providerServices->take(2)->pluck('service.name')->implode(', ') }} 
                                    @if($provider->providerServices->count() > 2) +{{ $provider->providerServices->count() - 2 }} more @endif
                                </p>
                            @endif
                        </div>
                        
                        <!-- CTA -->
                        <div class="px-6 py-4 border-t border-gray-50 bg-gray-50 text-center text-sm font-semibold text-amber-600 group-hover:bg-amber-50 transition">
                            View Full Profile &rarr;
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <h3 class="text-lg font-medium text-gray-900">No Professionals Found</h3>
                        <p class="mt-1 text-gray-500">Check back later as we grow our community.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $providers->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>