<nav x-data="{ openSidebar: false }" class="bg-white shadow-sm p-4">
    <x-stack-layout>
        <a class="max-w-[100px] grow"
           href="{{ url('/') }}">
            <x-logos.light-logo />
        </a>

        <button class="block md:hidden"
                type="button"
                aria-label="{{ __('Toggle navigation') }}">
            <x-icons.menu width="32px"
                          height="32px" />
        </button>

        <x-stack-layout class="grow">
            <!-- Left Side Of Navbar -->
            <x-stack-layout class="grow">
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
        </x-stack-layout>
    </x-stack-layout>
</nav>
