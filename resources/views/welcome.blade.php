<x-layouts.app title="Home | ServEase">
    <!-- Hero Section -->
    <div class="relative bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                    <div class="sm:text-center lg:text-left">
                        <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                            <span class="block xl:inline">Professional services</span>
                            <span class="block text-amber-600 xl:inline">at your doorstep</span>
                        </h1>
                        <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                            Find trusted local professionals for your home, business, and personal needs. Fast, reliable, and verified.
                        </p>
                        <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                            <div class="rounded-md shadow">
                                <a href="#services" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 md:py-4 md:text-lg md:px-10">
                                    Find a Service
                                </a>
                            </div>
                            <div class="mt-3 sm:mt-0 sm:ml-3">
                                <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-amber-700 bg-amber-100 hover:bg-amber-200 md:py-4 md:text-lg md:px-10">
                                    Become a Provider
                                </a>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2 bg-gray-100 flex items-center justify-center">
            <div class="text-gray-400 text-center p-10">
                <svg class="mx-auto h-24 w-24 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <p class="mt-2 text-sm">Illustration / Image</p>
            </div>
        </div>
    </div>

    <!-- Services Discovery Section -->
    <div id="services" class="bg-gray-50 py-16 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="sm:text-center">
                <h2 class="text-base text-amber-600 font-semibold tracking-wide uppercase">Our Services</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    What do you need help with?
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 sm:mx-auto">
                    Browse through our categories and find the right professional for your job.
                </p>
            </div>

            <div class="mt-12 space-y-12 sm:space-y-0 sm:grid sm:grid-cols-2 sm:gap-x-6 sm:gap-y-12 lg:grid-cols-3 lg:gap-x-8">
                
                @forelse($categories as $category)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $category->name }}</h3>
                            @if($category->description)
                                <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $category->description }}</p>
                            @endif
                            
                            <ul class="mt-4 space-y-2">
                                @forelse($category->services->take(4) as $service)
                                    <li class="flex items-center">
                                        <svg class="h-5 w-5 text-amber-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <!-- UPDATED LINK -->
                                        <a href="{{ route('services.show', $service->slug) }}" class="text-gray-700 hover:text-amber-600 text-sm font-medium transition">{{ $service->name }}</a>
                                    </li>
                                @empty
                                    <li class="text-sm text-gray-400 italic">No specific services listed yet.</li>
                                @endforelse
                            </ul>
                            
                            @if($category->services->count() > 4)
                                <div class="mt-4">
                                    <a href="#" class="text-sm text-amber-600 font-medium hover:text-amber-500">View all {{ $category->services->count() }} services &rarr;</a>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No Categories Found</h3>
                        <p class="mt-1 text-sm text-gray-500">Currently, there are no service categories available.</p>
                    </div>
                @endforelse

            </div>
        </div>
    </div>
</x-layouts.app>