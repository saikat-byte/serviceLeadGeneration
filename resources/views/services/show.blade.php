<x-layouts.app title="{{ $service->name }} | ServEase">
    <div class="bg-gray-50 py-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Breadcrumb -->
            <nav class="flex mb-8" aria-label="Breadcrumb">
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
                            <span class="ml-4 text-sm font-medium text-gray-500">{{ $service->category->name }}</span>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-4 text-sm font-medium text-gray-700">{{ $service->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                <!-- Left Column: Service Details -->
                <div class="lg:col-span-7">
                    <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-200 mb-6">
                        <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">{{ $service->name }}</h1>
                        <div class="mt-4 prose prose-amber text-gray-600">
                            <p>{{ $service->description ?? 'Professional '.$service->name.' services provided by verified experts.' }}</p>
                        </div>
                        
                        <div class="mt-8 border-t border-gray-200 pt-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Why choose our professionals?</h3>
                            <ul class="space-y-4">
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <p class="ml-3 text-base text-gray-700">Verified & Background Checked Experts</p>
                                </li>
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <p class="ml-3 text-base text-gray-700">Transparent Pricing & Secure Payments</p>
                                </li>
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <p class="ml-3 text-base text-gray-700">Quality Service Guarantee</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Request Form -->
                <div class="lg:col-span-5 mt-8 lg:mt-0">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden sticky top-8">
                        <div class="bg-amber-600 px-6 py-4">
                            <h3 class="text-lg font-bold text-white">Request this Service</h3>
                            <p class="text-amber-100 text-sm mt-1">Get matched with available professionals.</p>
                        </div>
                        <div class="p-6">
                            @guest
                                <div class="text-center py-6">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    <h4 class="text-base font-medium text-gray-900 mb-2">Login Required</h4>
                                    <p class="text-sm text-gray-500 mb-6">Please log in to your account to request a service.</p>
                                    <a href="{{ route('auth.google') }}" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                        Log In with Google
                                    </a>
                                </div>
                            @endguest

                            @auth
                                @if($locations->isEmpty())
                                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                                        <div class="flex">
                                            <div class="ml-3">
                                                <p class="text-sm text-yellow-700">
                                                    You need to add a location/address to your profile before booking a service.
                                                </p>
                                               <p class="mt-2 text-sm">
                                                    <a href="{{ route('locations.create', ['redirect' => url()->current()]) }}" class="font-medium text-yellow-700 underline hover:text-yellow-600">Add Location</a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <form action="{{ route('service-requests.store') }}" method="POST" class="space-y-5">
                                        @csrf
                                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                                        
                                        @if($service->variants->count() > 0)
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Service Variant (Optional)</label>
                                                <select name="service_variant_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-md">
                                                    <option value="">Select a variant...</option>
                                                    @foreach($service->variants as $variant)
                                                        <option value="{{ $variant->id }}">{{ $variant->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('service_variant_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                        @endif

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Service Location <span class="text-red-500">*</span></label>
                                            <select name="location_id" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-md">
                                                @foreach($locations as $location)
                                                    <option value="{{ $location->id }}">{{ $location->label ?? 'Home' }} - {{ $location->locality }}</option>
                                                @endforeach
                                            </select>
                                            @error('location_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Preferred Date</label>
                                                <input type="date" name="preferred_at" min="{{ date('Y-m-d') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                                                @error('preferred_at') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Urgency</label>
                                                <select name="urgency" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-md">
                                                    <option value="normal">Normal</option>
                                                    <option value="urgent">Urgent</option>
                                                    <option value="emergency">Emergency</option>
                                                </select>
                                                @error('urgency') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Description / Requirements</label>
                                            <textarea name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm" placeholder="Please describe what you need help with..."></textarea>
                                            @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors">
                                            Submit Service Request
                                        </button>
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