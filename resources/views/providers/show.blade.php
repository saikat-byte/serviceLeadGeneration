<x-layouts.app title="{{ $provider->name }} | Professional Profile | ServEase">
    <div class="bg-gray-50 py-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <x-breadcrumbs :links="[
                'Professionals' => route('providers.index'),
                $provider->name => null
            ]" />

            <div class="lg:grid lg:grid-cols-12 lg:gap-8 relative">
                
                <!-- Main Content -->
                <div class="lg:col-span-8 space-y-8">
                    
                    <!-- HERO SECTION -->
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
                        <div class="sm:flex sm:items-start sm:justify-between">
                            <div class="sm:flex sm:items-center">
                                @if($provider->avatar)
                                    <img src="{{ $provider->avatar }}" alt="{{ $provider->name }}" class="h-24 w-24 sm:h-32 sm:w-32 rounded-full border border-gray-200 object-cover shrink-0">
                                @else
                                    <div class="h-24 w-24 sm:h-32 sm:w-32 rounded-full bg-amber-50 border border-gray-200 flex items-center justify-center text-amber-600 text-4xl font-bold shrink-0">
                                        {{ substr($provider->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="mt-4 sm:mt-0 sm:ml-6">
                                    <h1 class="text-2xl sm:text-4xl font-extrabold text-gray-900 flex items-center gap-3">
                                        {{ $provider->name }}
                                        <!-- Verification Badge safely determined -->
                                        @if($provider->verifications->count() > 0)
                                            <svg class="h-6 w-6 text-emerald-500" fill="currentColor" viewBox="0 0 24 24" title="Verified Professional"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        @endif
                                    </h1>
                                    
                                    <div class="flex items-center flex-wrap gap-x-6 gap-y-2 mt-4">
                                        <!-- Rating from ProviderProfile -->
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-amber-400 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                            <span class="font-bold text-gray-900">{{ number_format(optional($provider->providerProfile)->rating_average ?? 0, 1) }}</span>
                                            <span class="text-gray-500 ml-1">Rating</span>
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <svg class="w-5 h-5 text-gray-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            <span class="font-bold text-gray-900">{{ optional($provider->providerProfile)->completed_jobs_count ?? 0 }}</span>
                                            <span class="ml-1">Jobs Done</span>
                                        </div>
                                        @if(optional($provider->providerProfile)->experience_years)
                                        <div class="flex items-center text-gray-600">
                                            <svg class="w-5 h-5 text-gray-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span class="font-bold text-gray-900">{{ $provider->providerProfile->experience_years }}</span>
                                            <span class="ml-1">Years Exp.</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Provider Bio (If exists) -->
                        @if(optional($provider->providerProfile)->bio)
                            <div class="mt-8 pt-8 border-t border-gray-100">
                                <h3 class="text-lg font-bold text-gray-900 mb-3">About</h3>
                                <p class="text-gray-600 whitespace-pre-line">{{ $provider->providerProfile->bio }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- OFFERED SERVICES -->
                    @if($provider->providerServices->count() > 0)
                        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">Services Offered</h3>
                            <div class="space-y-4">
                                @foreach($provider->providerServices as $ps)
                                    @if($ps->service)
                                        <div class="flex items-center justify-between p-5 border border-gray-100 rounded-xl hover:border-amber-300 transition group">
                                            <div>
                                                <div class="text-xs font-semibold text-amber-600 mb-1 uppercase tracking-wider">{{ $ps->service->category->name ?? 'Service' }}</div>
                                                <h4 class="text-lg font-bold text-gray-900 group-hover:text-amber-600 transition">{{ $ps->service->name }}</h4>
                                                @if($ps->starting_price)
                                                    <p class="text-sm text-gray-500 mt-1">Starting from {{ config('app.currency', '$') }}{{ number_format($ps->starting_price, 2) }}</p>
                                                @endif
                                            </div>
                                            <!-- Contextual CTA: Connects back to original marketplace request flow -->
                                            <a href="{{ route('services.show', $ps->service->slug ?? $ps->service->id) }}#book" class="hidden sm:inline-flex items-center px-4 py-2 border border-amber-600 rounded-lg text-sm font-semibold text-amber-600 bg-white hover:bg-amber-50 transition">
                                                Request
                                            </a>
                                            <!-- Mobile arrow -->
                                            <a href="{{ route('services.show', $ps->service->slug ?? $ps->service->id) }}" class="sm:hidden text-amber-500">
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- SKILLS & EXPERTISE -->
                    @if($provider->providerSkills->count() > 0)
                        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">Verified Skills</h3>
                            <div class="flex flex-wrap gap-3">
                                @foreach($provider->providerSkills as $pskill)
                                    @if($pskill->skill)
                                        <div class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-50 border border-gray-200">
                                            <span class="text-sm font-medium text-gray-800">{{ $pskill->skill->name }}</span>
                                            
                                            <!-- Check specific skill verification safely -->
                                            @php
                                                $skillStatus = $pskill->verification_status instanceof \BackedEnum ? $pskill->verification_status->value : $pskill->verification_status;
                                            @endphp
                                            @if($skillStatus === 'verified')
                                                <svg class="w-4 h-4 text-emerald-500 ml-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- REVIEWS -->
                    @if(isset($reviews) && $reviews->count() > 0)
                        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                Customer Reviews 
                                <span class="ml-3 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">{{ $reviews->count() }} reviews</span>
                            </h3>
                            <div class="space-y-6">
                                @foreach($reviews as $review)
                                    <div class="pb-6 border-b border-gray-100 last:border-0 last:pb-0">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 font-bold text-xs shrink-0">
                                                    {{ substr(optional($review->reviewer)->name ?? 'C', 0, 1) }}
                                                </div>
                                                <p class="font-bold text-gray-900 text-sm ml-3">{{ optional($review->reviewer)->name ?? 'Customer' }}</p>
                                            </div>
                                            <div class="flex text-amber-400">
                                                @for($i=0; $i<$review->rating; $i++)
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                @endfor
                                            </div>
                                        </div>
                                        @if($review->comment)
                                            <p class="text-gray-600 text-sm pl-11">{{ $review->comment }}</p>
                                        @endif
                                        <p class="text-xs text-gray-400 mt-2 pl-11">{{ $review->created_at->format('M d, Y') }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Sidebar -->
                <div class="lg:col-span-4 mt-8 lg:mt-0">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden sticky top-8">
                        
                        <!-- CTA Banner -->
                        <div class="p-6 bg-gray-50 border-b border-gray-100 text-center">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Hire {{ explode(' ', $provider->name)[0] }}</h3>
                            <p class="text-sm text-gray-500 mb-6">Select a service below to request a booking with this professional.</p>
                            <a href="#services" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 transition">
                                View Services to Request
                            </a>
                        </div>

                        <div class="p-6 space-y-6">
                            <!-- Service Areas -->
                            @if($provider->providerServiceAreas->count() > 0)
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3">Service Areas</h4>
                                    <ul class="space-y-2">
                                        @foreach($provider->providerServiceAreas as $area)
                                            <li class="flex items-center text-sm text-gray-600">
                                                <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                {{ $area->locality ?? 'Coverage Area' }} 
                                                @if($area->radius_km) <span class="text-gray-400 ml-1">({{ $area->radius_km }}km)</span> @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Trust Metrics Safe Display -->
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3">Quick Facts</h4>
                                <ul class="space-y-3">
                                    <li class="flex justify-between text-sm">
                                        <span class="text-gray-500">Response Rate</span>
                                        <span class="font-semibold text-gray-900">{{ optional($provider->providerProfile)->response_rate ?? 'N/A' }}%</span>
                                    </li>
                                    <li class="flex justify-between text-sm">
                                        <span class="text-gray-500">Joined Platform</span>
                                        <span class="font-semibold text-gray-900">{{ $provider->created_at->format('M Y') }}</span>
                                    </li>
                                </ul>
                            </div>
                            
                            <!-- Badges -->
                            <div class="pt-4 border-t border-gray-100 flex items-center justify-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    Secure Platform
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>