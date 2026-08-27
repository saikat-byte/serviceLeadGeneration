<x-layouts.app title="{{ $service->name }} | ServEase">
    <div class="bg-gray-50 py-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <x-breadcrumbs :links="[
                'Services' => route('services.index'),
                $service->category->name => route('categories.show', $service->category->slug ?? $service->category->id),
                $service->name => null
            ]" />

            <div class="lg:grid lg:grid-cols-12 lg:gap-8 relative">
                <!-- Left Column: Details -->
                <div class="lg:col-span-7 space-y-8">
                    
                    <!-- Service Hero Info -->
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 mb-4">{{ $service->category->name }}</span>
                        <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">{{ $service->name }}</h1>
                        <p class="mt-4 text-lg text-gray-600">{{ $service->description ?? 'Professional '.$service->name.' services provided by verified experts.' }}</p>
                        
                        <!-- Dynamic Pricing Model Display -->
                        @if($service->definition && $service->definition->pricing_type)
                            <div class="mt-6 inline-flex items-center px-4 py-2 rounded-lg bg-gray-50 border border-gray-100">
                                <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="font-medium text-gray-700">
                                    Pricing: 
                                    <span class="text-gray-900 font-bold capitalize">
                                        {{ str_replace('_', ' ', $service->definition->pricing_type->value ?? $service->definition->pricing_type) }}
                                    </span>
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Customer Requirements (If definition exists) -->
                    @if($service->definition && !empty($service->definition->customer_requirements))
                        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">What you need to provide</h3>
                            <ul class="space-y-3">
                                @foreach($service->definition->customer_requirements as $req)
                                    <li class="flex items-start">
                                        <svg class="h-6 w-6 text-emerald-500 shrink-0 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span class="text-gray-600">{{ $req }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Available Professionals Grid -->
                    @if(isset($service->providerServices) && $service->providerServices->count() > 0)
                        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">Professionals offering this service</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($service->providerServices as $ps)
                                    @if($ps->provider)
                                        <div class="flex items-center p-4 border border-gray-100 rounded-xl bg-gray-50">
                                            @if(optional($ps->provider)->avatar)
                                                <img src="{{ $ps->provider->avatar }}" alt="" class="h-12 w-12 rounded-full object-cover border border-gray-200">
                                            @else
                                                <div class="h-12 w-12 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 font-bold">
                                                    {{ substr($ps->provider->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div class="ml-4">
                                               <p class="text-sm font-bold text-gray-900 group-hover:text-amber-600 transition">
                                                    <a href="{{ route('providers.show', $ps->provider->id) }}" class="focus:outline-none">
                                                        <span class="absolute inset-0" aria-hidden="true"></span>
                                                        {{ $ps->provider->name }}
                                                    </a>
                                                </p>
                                                @if($ps->starting_price)
                                                    <p class="text-xs text-amber-600 font-medium">Starts from {{ config('app.currency', '$') }}{{ $ps->starting_price }}</p>
                                                @else
                                                    <p class="text-xs text-gray-500">Verified Provider</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Service Specific Reviews -->
                    @if(isset($reviews) && $reviews->count() > 0)
                        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">Recent Customer Reviews</h3>
                            <div class="space-y-6">
                                @foreach($reviews as $review)
                                    <div class="pb-6 border-b border-gray-100 last:border-0 last:pb-0">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="font-bold text-gray-900 text-sm">{{ optional($review->reviewer)->name ?? 'Customer' }}</p>
                                            <div class="flex text-amber-400">
                                                @for($i=0; $i<$review->rating; $i++)
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                @endfor
                                            </div>
                                        </div>
                                        @if($review->comment)
                                            <p class="text-gray-600 text-sm">{{ $review->comment }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Related Services -->
                    @if(isset($service->category->services) && $service->category->services->count() > 0)
                        <div class="bg-transparent pt-4">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">You might also need</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($service->category->services as $related)
                                    <a href="{{ route('services.show', $related->slug ?? $related->id) }}" class="p-4 bg-white rounded-xl shadow-sm border border-gray-100 hover:border-amber-300 transition group flex items-center justify-between">
                                        <span class="font-medium text-gray-800 group-hover:text-amber-600">{{ $related->name }}</span>
                                        <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Right Column: Booking Workflow Form -->
                <div class="lg:col-span-5 mt-8 lg:mt-0">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden sticky top-8">
                        <div class="bg-amber-600 px-6 py-5">
                            <h3 class="text-xl font-bold text-white">Request Service</h3>
                            <p class="text-amber-100 text-sm mt-1">Get matched with trusted professionals instantly.</p>
                        </div>
                        <div class="p-6">
                            @guest
                                <div class="text-center py-6">
                                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    <h4 class="text-base font-medium text-gray-900 mb-2">Login Required</h4>
                                    <p class="text-sm text-gray-500 mb-6">Please log in to your account to securely request this service.</p>
                                    <a href="{{ route('auth.google') }}" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 transition">
                                        Log In / Register
                                    </a>
                                </div>
                            @endguest

                            @auth
                                @if($locations->isEmpty())
                                    <div class="bg-yellow-50 border border-yellow-100 p-5 rounded-xl text-center">
                                        <p class="text-sm text-yellow-800 font-medium mb-3">Add a location to your profile to book services.</p>
                                        <a href="{{ route('locations.create', ['redirect' => url()->current()]) }}" class="inline-flex items-center justify-center px-4 py-2 border border-yellow-600 rounded-md shadow-sm text-sm font-medium text-yellow-700 bg-white hover:bg-yellow-50 transition">
                                            Add Location
                                        </a>
                                    </div>
                                @else
                                    <form action="{{ route('service-requests.store') }}" method="POST" class="space-y-6">
                                        @csrf
                                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                                        
                                        <!-- Visual Variants Section -->
                                        @if($service->variants->count() > 0)
                                            <div>
                                                <label class="block text-sm font-bold text-gray-900 mb-3">Select Option <span class="text-red-500">*</span></label>
                                                <div class="space-y-3">
                                                    @foreach($service->variants as $variant)
                                                        <label class="flex items-center p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-amber-400 focus-within:ring-2 focus-within:ring-amber-500 bg-gray-50 transition">
                                                            <input type="radio" name="service_variant_id" value="{{ $variant->id }}" required class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300">
                                                            <span class="ml-3 block text-sm font-medium text-gray-900">
                                                                {{ $variant->name }}
                                                            </span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                @error('service_variant_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                            </div>
                                        @endif

                                        <div>
                                            <label class="block text-sm font-bold text-gray-900 mb-2">Service Location <span class="text-red-500">*</span></label>
                                            <select name="location_id" required class="block w-full py-3 px-4 text-sm border-gray-300 focus:ring-amber-500 focus:border-amber-500 rounded-xl bg-gray-50">
                                                <option value="" disabled selected>Choose your saved location...</option>
                                                @foreach($locations as $location)
                                                    <option value="{{ $location->id }}">{{ $location->label ?? 'Home' }} - {{ $location->locality }}</option>
                                                @endforeach
                                            </select>
                                            @error('location_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-bold text-gray-900 mb-2">Preferred Date</label>
                                                <input type="date" name="preferred_at" min="{{ date('Y-m-d') }}" class="block w-full py-3 px-4 text-sm border-gray-300 rounded-xl bg-gray-50 focus:ring-amber-500 focus:border-amber-500">
                                                @error('preferred_at') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-bold text-gray-900 mb-2">Urgency</label>
                                                <select name="urgency" class="block w-full py-3 px-4 text-sm border-gray-300 rounded-xl bg-gray-50 focus:ring-amber-500 focus:border-amber-500">
                                                    <option value="normal">Normal</option>
                                                    <option value="urgent">Urgent</option>
                                                    <option value="emergency">Emergency</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-gray-900 mb-2">Additional Details</label>
                                            <textarea name="description" rows="3" class="block w-full py-3 px-4 text-sm border-gray-300 rounded-xl bg-gray-50 focus:ring-amber-500 focus:border-amber-500" placeholder="Any specific requirements..."></textarea>
                                        </div>

                                        <button type="submit" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors">
                                            Submit Service Request
                                        </button>
                                        <p class="text-xs text-center text-gray-500 mt-4">You won't be charged yet. Providers will review your request.</p>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>