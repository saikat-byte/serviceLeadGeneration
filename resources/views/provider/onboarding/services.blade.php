<x-layouts.app title="Select Services | Provider Onboarding">
    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6">
        
        <!-- Progress Bar (Component placeholder) -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Which services do you provide?</h2>
            <p class="text-gray-500">Select the services and optionally set your starting price. New services require admin approval.</p>
        </div>

        <form action="{{ route('provider.onboarding.services.save') }}" method="POST">
            @csrf
            <div class="space-y-8">
                @foreach($categories as $category)
                    @if($category->services->count() > 0)
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">{{ $category->name }}</h3>
                            <div class="space-y-4">
                                @foreach($category->services as $service)
                                    @php $isAssigned = $userServices->has($service->id); @endphp
                                    
                                    <label class="flex flex-col sm:flex-row sm:items-center p-4 border rounded-xl cursor-pointer transition {{ $isAssigned ? 'border-amber-500 bg-amber-50' : 'border-gray-200 hover:border-amber-300' }}">
                                        
                                        <div class="flex items-center flex-grow">
                                            <input type="checkbox" name="services[{{ $service->id }}][selected]" value="1" 
                                                class="h-5 w-5 text-amber-600 focus:ring-amber-500 rounded border-gray-300"
                                                {{ $isAssigned ? 'checked' : '' }}
                                                onchange="document.getElementById('price_div_{{ $service->id }}').classList.toggle('hidden', !this.checked)">
                                            <div class="ml-3">
                                                <span class="block text-sm font-bold text-gray-900">{{ $service->name }}</span>
                                                <span class="block text-xs text-gray-500">{{ Str::limit($service->description, 60) }}</span>
                                            </div>
                                        </div>

                                        <!-- Starting Price Input -->
                                        <div id="price_div_{{ $service->id }}" class="mt-3 sm:mt-0 sm:ml-4 flex items-center {{ $isAssigned ? '' : 'hidden' }}">
                                            <label class="text-xs text-gray-500 mr-2">Starting Price (₹)</label>
                                            <input type="number" step="0.01" name="services[{{ $service->id }}][starting_price]" 
                                                value="{{ $isAssigned ? $userServices[$service->id]->starting_price : '' }}"
                                                class="block w-24 px-3 py-1.5 text-sm border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500" placeholder="0.00">
                                        </div>
                                        
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-amber-600 text-white font-bold rounded-xl shadow-sm hover:bg-amber-700 transition">
                    Save Services & Continue
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>