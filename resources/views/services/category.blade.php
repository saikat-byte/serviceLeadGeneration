<x-layouts.app title="{{ $category->name }} Services | ServEase">
    <div class="bg-gray-50 py-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <x-breadcrumbs :links="[
                'Services' => route('services.index'),
                $category->name => null
            ]" />

            <!-- Category Hero -->
            <div class="bg-white rounded-2xl p-8 sm:p-12 shadow-sm border border-gray-100 mb-10 flex flex-col sm:flex-row items-center gap-8">
                <div class="h-24 w-24 sm:h-32 sm:w-32 bg-amber-50 rounded-full flex items-center justify-center shrink-0">
                    <svg class="h-12 w-12 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div class="text-center sm:text-left">
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900">{{ $category->name }}</h1>
                    <p class="mt-4 text-gray-500 max-w-2xl text-lg">{{ $category->description ?? 'Explore professional and verified services in the ' . $category->name . ' category.' }}</p>
                </div>
            </div>

            <!-- Sub Categories (If any) -->
            @if($category->children->count() > 0)
                <div class="mb-10">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Explore Sub-categories</h3>
                    <div class="flex flex-wrap gap-3">
                        @foreach($category->children as $child)
                            <a href="{{ route('categories.show', $child->slug ?? $child->id) }}" class="px-5 py-2.5 bg-white border border-gray-200 rounded-full text-sm font-medium text-gray-700 hover:border-amber-400 hover:text-amber-600 shadow-sm transition">
                                {{ $child->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Services Grid -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Available Services</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($services as $service)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-amber-300 transition-all duration-200 flex flex-col overflow-hidden group">
                            <div class="p-6 flex-grow">
                                <h3 class="text-lg font-bold text-gray-900 group-hover:text-amber-600 transition-colors mb-2">
                                    <a href="{{ route('services.show', $service->slug ?? $service->id) }}" class="focus:outline-none">
                                        <span class="absolute inset-0" aria-hidden="true"></span>
                                        {{ $service->name }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-500 line-clamp-3 mb-4">
                                    {{ $service->description ?? 'Professional service provided by our verified experts.' }}
                                </p>
                            </div>
                            <div class="px-6 py-4 border-t border-gray-50 bg-gray-50 flex justify-between items-center group-hover:bg-amber-50 transition-colors">
                                <span class="text-sm font-semibold text-amber-600">View Details</span>
                                <svg class="w-5 h-5 text-amber-600 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                            <h3 class="text-lg font-medium text-gray-900">No services available yet</h3>
                            <p class="mt-1 text-gray-500">Check back later for new services in this category.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-8">
                    {{ $services->links() }}
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>