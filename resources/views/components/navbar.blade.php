<nav class="bg-white border-b border-gray-100" x-data="{ mobileMenuOpen: false, profileDropdownOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="/" class="text-xl font-bold text-amber-600">
                        ServEase
                    </a>
                </div>
                <!-- Desktop Links -->
                <div class="hidden sm:-my-px sm:ml-10 sm:flex sm:space-x-8">
                    <a href="/"
                        class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">Services</a>
                    <a href="{{ route('how-it-works') }}"
                        class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">How
                        it Works</a>
                </div>
            </div>

            <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-4">
                @guest
                    <a href="{{ route('auth.google') }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium">Log
                        in</a>
                    <a href="{{ route('auth.google') }}"
                        class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 focus:outline-none focus:border-amber-700 focus:ring ring-amber-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Sign up with Google
                    </a>
                @endguest

                @auth
                    <!-- Notification Bell (Desktop) -->
                    <div class="relative ml-3 group" x-data="{ notifDropdownOpen: false }">
                        <!-- Removed @click.away from button to prevent double triggering -->
                        <button @click="notifDropdownOpen = !notifDropdownOpen"
                            class="relative p-2 text-gray-500 hover:text-amber-600 transition focus:outline-none">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span
                                    class="absolute top-1 right-1 flex items-center justify-center h-4 w-4 rounded-full bg-red-500 text-white text-[10px] font-bold">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>

                        <!-- Notification Dropdown -->
                        <div x-show="notifDropdownOpen" @click.outside="notifDropdownOpen = false" x-transition x-cloak
                            class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-xl py-1 z-50 ring-1 ring-black ring-opacity-5">
                            <div
                                class="px-4 py-2 border-b border-gray-100 font-semibold text-gray-700 flex justify-between items-center">
                                <span>Notifications</span>
                            </div>
                            <div class="max-h-64 overflow-y-auto">
                                @forelse(auth()->user()->notifications->take(5) as $notification)
                                    @php
                                        // Handle dynamic link based on notification type
                                        $link = isset($notification->data['booking_id'])
                                            ? url('/bookings/' . $notification->data['booking_id'])
                                            : (isset($notification->data['link']) ? $notification->data['link'] : '#');
                                    @endphp
                                    <a href="{{ $link }}"
                                        class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-50">
                                        <p class="font-medium {{ $notification->read_at ? 'text-gray-500' : 'text-gray-900' }}">
                                            {{ $notification->data['message'] ?? 'New notification' }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </a>
                                    @php $notification->markAsRead(); @endphp
                                @empty
                                    <div class="px-4 py-3 text-sm text-gray-500 text-center">No notifications yet</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Profile dropdown FIX -->
                    <div class="ml-3 relative">
                        <!-- Profile Image Button -->
                        <div>
                            <button @click="profileDropdownOpen = !profileDropdownOpen" type="button"
                                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
                                id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                <img class="h-8 w-8 rounded-full object-cover"
                                    src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}"
                                    alt="{{ auth()->user()->name }}">
                            </button>
                        </div>

                        <!-- Profile Dropdown Menu -->
                        <div x-show="profileDropdownOpen" @click.outside="profileDropdownOpen = false" x-transition x-cloak
                            class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50 focus:outline-none"
                            role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                            
                            @php
                                $roleCheck = auth()->user()->role instanceof \BackedEnum ? auth()->user()->role->value : auth()->user()->role;
                                $rolePrefix = $roleCheck === 'provider' ? 'provider.' : 'customer.';
                            @endphp

                            @if($roleCheck === 'admin' || auth()->user()->hasRole('super_admin'))
                                <a href="/admin" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Admin Panel</a>
                            @elseif($roleCheck === 'provider')
                                <a href="{{ route('provider.dashboard') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Provider Dashboard</a>
                            @else
                                <a href="{{ route('dashboard') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Dashboard</a>
                            @endif

                            <a href="{{ route($rolePrefix . 'profile.edit') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Profile</a>
                            <a href="{{ route($rolePrefix . 'complaints.index') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Support & Complaints</a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign
                                    out</button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': mobileMenuOpen, 'inline-flex': !mobileMenuOpen }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !mobileMenuOpen, 'inline-flex': mobileMenuOpen }" class="hidden"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" class="sm:hidden" x-cloak>
        <div class="pt-2 pb-3 space-y-1">
            <a href="/"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 transition duration-150 ease-in-out">Services</a>
            <a href="{{ route('how-it-works') }}"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 transition duration-150 ease-in-out">How
                it Works</a>
        </div>

        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="flex items-center px-4 justify-between">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <img class="h-10 w-10 rounded-full object-cover"
                                src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}"
                                alt="{{ auth()->user()->name }}">
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">{{ auth()->user()->name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ auth()->user()->email }}</div>
                        </div>
                    </div>

                    <!-- Notification Bell (Mobile) -->
                    <button class="relative p-2 text-gray-500 hover:text-amber-600 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span
                                class="absolute top-1 right-1 flex items-center justify-center h-4 w-4 rounded-full bg-red-500 text-white text-[10px] font-bold">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>
                </div>

                <div class="mt-3 space-y-1">
                    @php
                        $roleCheck = auth()->user()->role instanceof \BackedEnum ? auth()->user()->role->value : auth()->user()->role;
                        $rolePrefix = $roleCheck === 'provider' ? 'provider.' : 'customer.';
                    @endphp

                    @if($roleCheck === 'admin' || auth()->user()->hasRole('super_admin'))
                        <a href="/admin"
                            class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">Admin
                            Panel</a>
                    @elseif($roleCheck === 'provider')
                        <a href="{{ route('provider.dashboard') }}"
                            class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">Provider
                            Dashboard</a>
                    @else
                        <a href="{{ route('dashboard') }}"
                            class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">My
                            Dashboard</a>
                    @endif

                    <a href="{{ route($rolePrefix . 'profile.edit') }}"
                        class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">My
                        Profile</a>
                    <a href="{{ route($rolePrefix . 'complaints.index') }}"
                        class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">Support
                        & Complaints</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">Sign
                            out</button>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="space-y-1">
                    <a href="{{ route('auth.google') }}"
                        class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">Log
                        in / Sign up</a>
                </div>
            </div>
        @endauth
    </div>
</nav>