<div class="relative bg-white overflow-hidden">
    <!-- Background Pattern/Gradient -->
    <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-white -z-10"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-24 md:pt-24 md:pb-32 lg:flex lg:items-center lg:gap-12">
        
        <!-- Left Content -->
        <div class="text-center lg:text-left lg:w-1/2">
            <span class="text-sm font-bold tracking-wider text-amber-600 uppercase">Premium Local Services</span>
            <h1 class="mt-4 text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                <span class="block">Trusted Professionals</span>
                <span class="block text-amber-600 mt-1">at your doorstep</span>
            </h1>
            <p class="mt-6 text-lg text-gray-500 sm:max-w-xl sm:mx-auto lg:mx-0">
                Find verified local experts for your home, business, and personal needs. Fast, reliable, and hassle-free booking.
            </p>

            <!-- Search Card (Updated for Batch 17) -->
            <form action="{{ route('services.index') }}" method="GET" class="mt-8 bg-white p-4 rounded-xl shadow-lg border border-gray-100 sm:flex sm:items-center gap-3">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <!-- Added name="q" and value retention -->
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="What service do you need?" class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm">
                </div>
                <div class="mt-3 sm:mt-0 relative flex-grow sm:max-w-[200px]">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <!-- Added name="location" -->
                    <input type="text" name="location" value="{{ request('location') }}" placeholder="Your Location" class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm">
                </div>
                <button type="submit" class="mt-3 sm:mt-0 w-full sm:w-auto px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition shadow-sm">
                    Search
                </button>
            </form>

            <!-- Trust Signals -->
            <div class="mt-8 flex flex-wrap justify-center lg:justify-start gap-4 text-sm text-gray-600 font-medium">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-emerald-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Verified Professionals
                </div>
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-emerald-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Secure Booking
                </div>
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-emerald-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Transparent Pricing
                </div>
            </div>
        </div>

        <!-- Right Image/Visual -->
        <div class="mt-12 lg:mt-0 lg:w-1/2 relative hidden md:block">
            <!-- Placeholder for professional marketplace image -->
            <div class="relative rounded-2xl shadow-xl overflow-hidden aspect-[4/3] bg-gray-100 border border-gray-200 group">
                <img src="https://images.unsplash.com/photo-1581578731548-c64695cc6952?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Professional Cleaning Service" class="object-cover w-full h-full group-hover:scale-105 transition duration-700" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                <div class="absolute bottom-6 left-6 text-white">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-amber-500 text-xs font-bold px-2 py-1 rounded-md uppercase">Top Rated</span>
                    </div>
                    <h3 class="font-bold text-xl">Home Cleaning</h3>
                </div>
            </div>
        </div>
    </div>
</div>