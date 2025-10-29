<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('discover') }}">
                        <span class="font-bold text-2xl text-gray-900 dark:text-gray-100">
                            Where is
                            <span class="text-blue-600 dark:text-blue-400 ml-1">streaming?</span>
                        </span>
                    </a>
                </div>

                {{-- This is HIDDEN on mobile (by 'hidden') and SHOWN on desktop (by 'sm:flex') --}}
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('discover')" :active="request()->routeIs('discover')">
                        {{ __('Discover') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="flex items-center">
                {{-- This is HIDDEN on mobile (by 'hidden') and SHOWN on desktop (by 'sm:flex') --}}
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    @guest
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition duration-150 ease-in-out">
                            Log in
                        </a>
                        <a href="{{ route('register') }}" class="ms-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            Register
                        </a>
                    @else
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition duration-150 ease-in-out">
                                {{ __('Log Out') }}
                            </a>
                        </form>
                    @endguest
                </div>

                {{-- This is SHOWN on mobile (by 'flex') and HIDDEN on desktop (by 'sm:hidden') --}}
                <div class="-me-2 ms-4 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        {{-- Mobile Nav Links --}}
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('discover')" :active="request()->routeIs('discover')">
                {{ __('Discover') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-3 border-t border-gray-200 dark:border-gray-600">
            @guest
                {{-- These are the links that will appear for guests --}}
                <div class="px-4 space-y-3">
                    <a href="{{ route('login') }}" class="block w-full px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
                        Log in
                    </a>
                    <a href="{{ route('register') }}" class="block w-full px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
                        Register
                    </a>
                </div>
            @else
                {{-- This will appear for logged-in users --}}
                <div class="mt-3 space-y-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @endguest
        </div>
    </div>
</nav>