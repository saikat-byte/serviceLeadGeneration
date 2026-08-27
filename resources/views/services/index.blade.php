<x-layouts.app title="Explore Services | ServEase">
    <div class="bg-gray-50 py-8 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header & Breadcrumb -->
            <div class="mb-8">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-4">
                        <li>
                            <a href="/" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                </svg>
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="ml-4 text-sm font-medium text-gray-500">All Services</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-extrabold text-gray-900">Explore Services</h1>
                <p class="mt-2 text-gray-500">Find and book the best professionals for your needs.</p>
            </div>

            <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                
                <!-- Left Sidebar: Filters -->
                <div class="hidden lg:block lg:col-span-3">
                    <form action="{{ route('services.index') }}" method="GET" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 sticky top-8">
                        <!-- Preserve Search Query if exists -->
                        @if(request('q'))
                            <input type="hidden" name="q" value="{{ request('q') }}">
                        @endif
                        @if(request('location'))
                            <input type="hidden" name="location" value="{{ request('location') }}">
                        @endif

                        <h3 class="text-lg font-bold text-gray-900 mb-4">Categories</h3>
                        <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                            <div class="flex items-center">
                                <input type="radio" name="category" value="" id="cat_all" onchange="this.form.submit()" 
                                    class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 cursor-pointer"
                                    {{ !request('category') ? 'checked' : '' }}>
                                <label for="cat_all" class="ml-3 text-sm text-gray-700 cursor-pointer">All Categories</label>
                            </div>
                            
                            @foreach($categories as $cat)
                                <div class="flex items-center">
                                    <input type="radio" name="category" value="{{ $cat->id }}" id="cat_{{ $cat->id }}" onchange="this.form.submit()" 
                                        class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 cursor-pointer"
                                        {{ request('category') == $cat->id ? 'checked' : '' }}>
                                    <label for="cat_{{ $cat->id }}" class="ml-3 text-sm text-gray-700 cursor-pointer">{{ $cat->name }}</label>
                                </div>
                            @endforeach
                        </div>

                        @if(request('q') || request('category') || request('location'))
                            <div class="mt-6 pt-4 border-t border-gray-100">
                                <a href="{{ route('services.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                    Clear All Filters
                                </a>
                            </div>
                        @endif
                    </form>
                </div>

                <!-- Right Main Content: Service Grid -->
                <div class="lg:col-span-9 mt-6 lg:mt-0">
                    
                    <!-- Mobile Filter Toggle (Visible only on mobile) -->
                    <div class="lg:hidden mb-4">
                        <form action="{{ route('services.index') }}" method="GET" class="flex gap-2">
                            <select name="category" onchange="this.form.submit()" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-md shadow-sm">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                            @if(request('location')) <input type="hidden" name="location" value="{{ request('location') }}"> @endif
                        </form>
                    </div>

                    <!-- Search Results Info -->
                    @if(request('q'))
                        <div class="mb-6 bg-white p-4 rounded-lg border border-amber-200 text-amber-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Showing results for: <span class="font-bold ml-1">"{{ request('q') }}"</span>
                        </div>
                    @endif

                    <!-- Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                        @forelse($services as $service)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-amber-300 transition-all duration-200 flex flex-col overflow-hidden group">
                                <div class="p-5 flex-grow">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 mb-3">
                                        {{ $service->category->name }}
                                    </span>
                                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-amber-600 transition-colors mb-2">
                                        <a href="{{ route('services.show', $service->slug ?? $service->id) }}" class="focus:outline-none">
                                            <span class="absolute inset-0" aria-hidden="true"></span>
                                            {{ $service->name }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500 line-clamp-3">
                                        {{ $service->description ?? 'Professional service provided by our verified experts. Book now for a hassle-free experience.' }}
                                    </p>
                                </div>
                                <div class="px-5 py-4 border-t border-gray-50 mt-auto bg-gray-50 group-hover:bg-amber-50 transition-colors">
                                    <span class="text-sm font-semibold text-amber-600 flex items-center">
                                        View Details 
                                        <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </span>
                                </div>
                            </div>
                        @empty
                            <!-- Empty State -->
                            <div class="col-span-full bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                                <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">No Services Found</h3>
                                <p class="text-gray-500 max-w-md mx-auto mb-6">We couldn't find any services matching your search criteria. Try adjusting your keywords or filters.</p>
                                <a href="{{ route('services.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700">
                                    Clear Filters & View All
                                </a>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $services->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-layouts.app>