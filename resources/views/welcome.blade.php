<x-layouts.app title="ServEase | Premium Local Services at Your Doorstep">
     
    <!-- 01 & 02: Header/Nav is in App Layout, Hero Section -->  
     <x-home.hero />

    <!-- 04: Service Categories Section -->
    <div id="services" class="bg-gray-50 py-16 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-10">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900">Explore Categories</h2>
                    <p class="mt-2 text-gray-500">Find exactly what you need from our verified categories.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($categories as $category)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-amber-200 transition-all duration-200 group flex flex-col h-full overflow-hidden">
                        
                        <!-- Fixed: Removed external ui-avatars dependency. Replaced with clean local UI fallback -->
                        <div class="h-32 bg-amber-50 w-full flex items-center justify-center border-b border-gray-100">
                            <svg class="h-12 w-12 text-amber-300 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>

                        <div class="p-5 flex-grow flex flex-col">
                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-amber-600 transition-colors">{{ $category->name }}</h3>
                            @if($category->description)
                                <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $category->description }}</p>
                            @endif
                            
                            <ul class="mt-4 space-y-2 mb-4 flex-grow">
                                @forelse($category->services as $service)
                                    <li class="flex items-start">
                                        <svg class="h-4 w-4 text-amber-500 mr-2 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <a href="{{ route('services.show', $service->slug ?? $service->id) }}" class="text-sm text-gray-600 hover:text-amber-600 transition">{{ $service->name }}</a>
                                    </li>
                                @empty
                                    <li class="text-sm text-gray-400 italic">Services coming soon</li>
                                @endforelse
                            </ul>
                            
                            @if($category->services_count > 4)
                                <!-- Fixed: Dead link replaced with actual valid route -->
                                <a href="{{ route('services.index', ['category' => $category->id]) }}" class="mt-auto text-sm font-semibold text-amber-600 hover:text-amber-700">View all {{ $category->services_count }} services &rarr;</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                        <p class="text-gray-500">No categories found at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 06: How It Works -->
    <x-home.how-it-works />

    <!-- 08: Featured Professionals -->
    @if(isset($topProviders) && $topProviders->count() > 0)
    <div class="bg-gray-50 py-16 sm:py-24 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold text-gray-900 text-center mb-2">Top Rated Professionals</h2>
            <p class="text-gray-500 text-center mb-12">Meet our most reliable and highly rated service providers.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($topProviders as $provider)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center hover:shadow-md transition">
                        <!-- Removed ui-avatars. Used local fallback for missing avatars safely -->
                        @if(optional($provider->user)->avatar)
                            <img class="w-20 h-20 rounded-full mx-auto object-cover mb-4 ring-4 ring-amber-50" src="{{ $provider->user->avatar }}" alt="{{ $provider->user->name ?? 'Provider' }}" loading="lazy">
                        @else
                            <div class="w-20 h-20 rounded-full mx-auto mb-4 ring-4 ring-amber-50 bg-gray-200 flex items-center justify-center text-gray-500 text-xl font-bold">
                                {{ substr($provider->user->name ?? 'P', 0, 1) }}
                            </div>
                        @endif

                        <h3 class="text-lg font-bold text-gray-900">{{ $provider->user->name ?? 'Verified Pro' }}</h3>
                        
                        <div class="flex items-center justify-center mt-2 space-x-1">
                            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <span class="text-sm font-semibold text-gray-700">{{ number_format($provider->rating_average, 1) }}</span>
                            <span class="text-xs text-gray-400">({{ $provider->completed_jobs_count }} jobs)</span>
                        </div>
                        
                        @if($provider->experience_years)
                            <p class="text-xs text-gray-500 mt-2">{{ $provider->experience_years }} Years Experience</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- 11: Customer Reviews -->
    @if(isset($reviews) && $reviews->count() > 0)
    <div class="bg-white py-16 sm:py-24 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold text-gray-900 text-center mb-12">What Our Customers Say</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($reviews as $review)
                    <div class="bg-gray-50 rounded-xl p-6 relative">
                        <div class="flex text-amber-400 mb-4">
                            @for($i = 0; $i < $review->rating; $i++)
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            @endfor
                        </div>
                        
                        @if($review->comment)
                            <p class="text-gray-600 text-sm italic mb-4">"{{ Str::limit($review->comment, 100) }}"</p>
                        @else
                            <p class="text-gray-400 text-sm italic mb-4">Rating provided without comment.</p>
                        @endif

                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center text-amber-700 font-bold text-sm">
                                {{ substr(optional($review->reviewer)->name ?? 'C', 0, 1) }}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-bold text-gray-900">{{ optional($review->reviewer)->name ?? 'Customer' }}</p>
                                <!-- Fixed: Removed unverified "Verified Booking" hardcoded text -->
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- 16: Final CTA -->
    <div class="bg-amber-600">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
            <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                <span class="block">Ready to get started?</span>
                <span class="block text-amber-100 mt-1 text-2xl font-semibold">Join thousands of satisfied customers today.</span>
            </h2>
            <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0 gap-3">
                <a href="{{ route('services.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-amber-600 bg-white hover:bg-gray-50 shadow-sm transition">
                    Find a Service
                </a>
                <a href="{{ route('auth.google') }}" class="inline-flex items-center justify-center px-6 py-3 border border-white text-base font-medium rounded-lg text-white hover:bg-amber-700 transition">
                    Become a Provider
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>