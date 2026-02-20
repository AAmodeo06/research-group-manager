<nav x-data="{ open: false }" class="bg-white border-b border-secondary-300 h-16">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-6 h-full">
        <div class="flex items-center justify-between h-full">

            <div class="flex items-center gap-8 h-full">
                <!-- Logo -->
                <div class="shrink-0 flex items-center h-full">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-primary-600" />
                    </a>
                </div>

                {{-- Modificato da Andrea Amodeo --}}
                <!-- Navigation Links -->
                @auth
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex h-full items-center">

                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(auth()->user()->global_role === 'pi')
                        <x-nav-link :href="route('group.show')" :active="request()->routeIs('group.show')">
                            {{ __('Il mio gruppo') }}
                        </x-nav-link>
                    @endif

                    <x-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.*')">
                        {{ __('Projects') }}
                    </x-nav-link>

                    <x-nav-link :href="route('publications.index')" :active="request()->routeIs('publications.*')">
                        {{ __('Publications') }}
                    </x-nav-link>

                    <x-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')">
                        {{ __('Tasks') }}
                    </x-nav-link>
                </div>
                @endauth
                {{-- Fine modifica --}}
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex items-center h-full sm:ms-6">
                @auth

                <!-- Notifications + User -->
                <div class="flex items-center h-full gap-4">

                    {{-- Modificato da Andrea Amodeo --}}
                    <!-- Notifications -->
                    <div class="relative flex items-center h-full">
                        <x-dropdown align="right" width="80">
                            <x-slot name="trigger">
                                <button
                                    class="relative inline-flex items-center px-3 py-2
                                           text-secondary-500 hover:text-primary-600
                                           transition focus:outline-none">

                                    <!-- Icona notifiche -->
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2
                                            c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 01-6 0" />
                                    </svg>

                                    @if(auth()->user()->unreadNotifications->count())
                                        <span
                                            class="absolute -top-1 -right-1 bg-primary-600 text-white text-[10px] rounded-full px-1 leading-tight">
                                            {{ auth()->user()->unreadNotifications->count() }}
                                        </span>
                                    @endif
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                @forelse(auth()->user()->unreadNotifications as $notification)
                                    <form method="POST"
                                        action="{{ route('notifications.read', $notification->id) }}">
                                       @csrf
                                       <button
                                            class="w-full text-left px-4 py-2 hover:bg-secondary-100 transition">
                                            <div class="font-medium text-sm text-secondary-800">
                                                {{ $notification->data['task_title'] ?? '' }}
                                            </div>
                                            <div class="text-xs text-secondary-500">
                                                {{ $notification->data['project_title'] ?? '' }}
                                            </div>
                                        </button>
                                    </form>
                                @empty
                                    <div class="px-4 py-2 text-sm text-secondary-500">
                                        Nessuna notifica
                                    </div>
                                @endforelse
                            </x-slot>
                        </x-dropdown>
                    </div>
                    {{-- Fine modifica --}}

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md
                                       text-secondary-700 bg-white hover:text-primary-600 transition">
                                <div>
                                    {{ Auth::user()->name }}
                                    <span class="text-xs text-secondary-500">({{ Auth::user()->roleLabel() }})</span>
                                </div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>

                </div>

                @else
                    <div class="flex items-center space-x-4 h-full">
                        <a href="{{ route('login') }}" class="text-secondary-600 hover:text-primary-600">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="text-secondary-600 hover:text-primary-600">
                            Register
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden h-full">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md
                           text-secondary-400 hover:text-secondary-600 hover:bg-secondary-100
                           focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }"
                              class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }"
                              class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">

        {{-- Modificato da Andrea Amodeo --}}
        @auth
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(auth()->user()->global_role === 'pi')
                <x-responsive-nav-link :href="route('group.show')" :active="request()->routeIs('group.show')">
                    {{ __('Il mio gruppo') }}
                </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.*')">
                {{ __('Projects') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('publications.index')" :active="request()->routeIs('publications.*')">
                {{ __('Publications') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')">
                {{ __('Tasks') }}
            </x-responsive-nav-link>
        </div>
        @endauth
        {{-- Fine modifica --}}

        <div class="pt-4 pb-1 border-t border-secondary-200">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-secondary-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-secondary-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="px-4">
                    <div class="font-medium text-base text-secondary-800">Ospite</div>
                    <div class="font-medium text-sm text-secondary-500">Non autenticato</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('login')">
                        {{ __('Login') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')">
                        {{ __('Register') }}
                    </x-responsive-nav-link>
                </div>
            @endauth
        </div>
    </div>
</nav>
