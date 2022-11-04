<x-partials.header>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="https://unpkg.com/ionicons@4.5.10-0/dist/css/ionicons.min.css" rel="stylesheet">
</x-partials.header>

<div id="app">
    @include('layouts.nav')

    <main class="py-4">
        @yield('content')
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->any())
            @foreach($errors->all() as $error)
                window.Notyf.error(@js($error));
            @endforeach
        @endif

        @if(session()->has('success'))
            window.Notyf.success(@js(session()->get('success')));
        @endif

        @if(session()->has('debug'))
            console.log('Request returned the following debug information:')
            console.log(@js(session()->get('debug')));
        @endif
    });
</script>

<x-partials.footer />
