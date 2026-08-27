<x-layouts.app>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="text-xl font-bold text-gray-800">My Profile Settings</h2>
                <p class="text-sm text-gray-500">Update your account information and avatar.</p>
            </div>

            <div class="p-6">
                @php
                    $rolePrefix = (auth()->user()->role instanceof \BackedEnum ? auth()->user()->role->value : auth()->user()->role) === 'provider' ? 'provider.' : 'customer.';
                @endphp
                <form action="{{ route($rolePrefix . 'profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Avatar Section -->
                    <div class="mb-6 flex items-center space-x-6">
                        <div class="shrink-0">
                            <img class="h-20 w-20 object-cover rounded-full border border-gray-200 shadow-sm" 
                                 src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" 
                                 alt="Current avatar">
                        </div>
                        <label class="block">
                            <span class="sr-only">Choose profile photo</span>
                            <input type="file" name="avatar" accept="image/*"
                                class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-amber-50 file:text-amber-700
                                hover:file:bg-amber-100 cursor-pointer"/>
                            <p class="text-xs text-gray-400 mt-1">PNG, JPG, GIF up to 2MB.</p>
                            @error('avatar') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <!-- Details Section -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email Address <span class="text-xs text-gray-400">(Cannot be changed)</span></label>
                            <input type="email" value="{{ $user->email }}" disabled
                                class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 shadow-sm sm:text-sm cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Mobile Number</label>
                            <input type="text" name="mobile" value="{{ old('mobile', $user->mobile) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm"
                                placeholder="e.g. 9876543210">
                            @error('mobile') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-8 pt-5 border-t border-gray-200">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent bg-amber-600 py-2 px-6 text-sm font-medium text-white shadow-sm hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>