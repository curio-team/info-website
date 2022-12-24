<nav x-data="{ openSidebar: false }" class="bg-white shadow-sm p-4">
    <x-stack-layout>
        <a class="max-w-[100px] grow"
           href="{{ url('/') }}">
            <x-logos.light-logo />
        </a>

        <button class="block md:hidden"
                type="button"
                @click="openSidebar = true"
                aria-label="{{ __('Toggle navigation') }}">
            <x-icons.menu width="32px"
                          height="32px" />
        </button>

        <div class="md:!block bg-white bottom-0 top-0 right-0 flex grow fixed md:static p-4 md:p-0 shadow-md md:shadow-none min-w-[50%] gap-4 flex-col md:flex-row"
            :class="{ 'hidden': !openSidebar }">
            <button class="block md:hidden self-end"
                    type="button"
                    @click="openSidebar = false"
                    aria-label="{{ __('Toggle navigation') }}">
                <x-icons.close width="32px"
                               height="32px"/>
            </button>

            <!-- Left Side Of Navbar -->
            <x-stack-layout class="md:grow">
                @auth
                @can('view-any', App\Models\Site::class)
                    <a href="{{ route('sites.index') }}">@lang('crud.studenten_info_sites.manage')</a>
                @endcan
                @endauth
            </x-stack-layout>

            <!-- Right Side Of Navbar -->
            <x-stack-layout>
                <!-- Authentication Links -->
                @guest
                    <a href="{{ route('login') }}">{{ __('Login') }}</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}">{{ __('Register') }}</a>
                    @endif
                @else
                    <a class="nav-link dropdown-toggle"
                    href="#">
                        {{ Auth::user()->name }} <span class="caret"></span>
                    </a>

                    <div>
                        <a href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form"
                            action="{{ route('logout') }}"
                            method="POST"
                            class="hidden">
                            @csrf
                        </form>
                    </div>
                @endguest
            </x-stack-layout>
        </div>
    </x-stack-layout>
</nav>
