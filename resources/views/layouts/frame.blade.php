<x-partials.header>
    <script src="{{ asset('js/app-viewer.js') }}"
            defer></script>
</x-partials.header>

{{-- NOTE: Do not remove the 'sandbox' attribute. It is what restricts user sites to not get access to cookies and such
--}}
<iframe sandbox="allow-scripts allow-popups allow-forms"
        id="siteFrame"
        class="website md:!pt-0"
        src="@yield('path_url')"
        frameborder="0"></iframe>

<script>
    const siteFrameEl = document.getElementById('siteFrame');

    // When the page has fully loaded, set the padding top of the site frame to the height of the header
    siteFrameEl.addEventListener('load', () => {
        const animatedEl = document.getElementById('animatedEl');
        const animatedElHeight = animatedEl.offsetHeight;
        siteFrameEl.style.paddingTop = `${animatedElHeight}px`;
    });
</script>

<div class="frame @unless(isset($_GET['noanim'])) animate @endunless"
     data-add-class-after-intro="!border-none !outline-none">
</div>

<div class="md:rounded-bl-3xl developer @unless(isset($_GET['noanim'])) animate @endunless"
     id="animatedEl"
     @if(isset($_GET['noanim']))
     data-no-intro
     @endif
     data-add-class-after-intro="shadow">
    <span class="introduction">@yield('intro_text')</span>
    <h2 class="student">
        <span>@yield('student_name'),</span>
        <small class="title">
            <strong>software developer</strong>
            <span class="title-text">
                @yield('title_text')
            </span>
        </small>
    </h2>
    @hasSection('is_test')
    <div class="i18n">
        <div class="flags @unless(isset($_GET['noanim'])) delay-animate @endunless">
            <a title="Bekijk een Nederlandstalige versie van onze site (gemaakt door een van onze studenten)">
                <svg xmlns="http://www.w3.org/2000/svg"
                     id="flag-icons-nl"
                     viewBox="0 0 640 480"
                     class="shadow">
                    <path fill="#21468b"
                          d="M0 0h640v480H0z" />
                    <path fill="#fff"
                          d="M0 0h640v320H0z" />
                    <path fill="#ae1c28"
                          d="M0 0h640v160H0z" />
                </svg>
            </a>
            <a title="View an English version of our site (made by one of our students)">
                <svg xmlns="http://www.w3.org/2000/svg"
                     id="flag-icons-gb"
                     viewBox="0 0 640 480"
                     class="shadow">
                    <path fill="#012169"
                          d="M0 0h640v480H0z" />
                    <path fill="#FFF"
                          d="m75 0 244 181L562 0h78v62L400 241l240 178v61h-80L320 301 81 480H0v-60l239-178L0 64V0h75z" />
                    <path fill="#C8102E"
                          d="m424 281 216 159v40L369 281h55zm-184 20 6 35L54 480H0l240-179zM640 0v3L391 191l2-44L590 0h50zM0 0l239 176h-60L0 42V0z" />
                    <path fill="#FFF"
                          d="M241 0v480h160V0H241zM0 160v160h640V160H0z" />
                    <path fill="#C8102E"
                          d="M0 193v96h640v-96H0zM273 0v480h96V0h-96z" />
                </svg>
            </a>
        </div>
    </div>
    @else
    <div class="i18n">
        <div
            class="flags @unless(isset($_GET['noanim'])) delay-animate @endunless">
            <a href="{{ route('home') }}"
               title="Bekijk een Nederlandstalige versie van onze site (gemaakt door een van onze studenten)">
                <svg xmlns="http://www.w3.org/2000/svg"
                     id="flag-icons-nl"
                     viewBox="0 0 640 480"
                     class="shadow">
                    <path fill="#21468b"
                          d="M0 0h640v480H0z" />
                    <path fill="#fff"
                          d="M0 0h640v320H0z" />
                    <path fill="#ae1c28"
                          d="M0 0h640v160H0z" />
                </svg>
            </a>
            <a href="{{ route('home.english') }}"
               title="View an English version of our site (made by one of our students)">
                <svg xmlns="http://www.w3.org/2000/svg"
                     id="flag-icons-gb"
                     viewBox="0 0 640 480"
                     class="shadow">
                    <path fill="#012169"
                          d="M0 0h640v480H0z" />
                    <path fill="#FFF"
                          d="m75 0 244 181L562 0h78v62L400 241l240 178v61h-80L320 301 81 480H0v-60l239-178L0 64V0h75z" />
                    <path fill="#C8102E"
                          d="m424 281 216 159v40L369 281h55zm-184 20 6 35L54 480H0l240-179zM640 0v3L391 191l2-44L590 0h50zM0 0l239 176h-60L0 42V0z" />
                    <path fill="#FFF"
                          d="M241 0v480h160V0H241zM0 160v160h640V160H0z" />
                    <path fill="#C8102E"
                          d="M0 193v96h640v-96H0zM273 0v480h96V0h-96z" />
                </svg>
            </a>
        </div>
    </div>
    @endif
</div>
<div class="branding shadow rounded-tr-3xl @hasSection('is_test') opacity-50 @endif"
     data-add-class-after-intro="!block">
    <a href="https://www.curio.nl/mbo/techniek-en-technologie/softwaredeveloper/"
       class="logo"
       target="_blank">
        <h1>
            <x-logos.light-logo class="w-[100px] md:w-[150px]" />
        </h1>
    </a>
</div>

<x-partials.footer />
