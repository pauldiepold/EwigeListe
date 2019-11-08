<!doctype html>
<html lang="de" class="tw-h-full">
<head>

    @include('layouts.head')

</head>

<body class="text-center position-relative"
      style="min-height: 100%; padding-bottom: 6.5rem;">

    <div id="app">

        @include('layouts.nav')

        <div class="container tw-py-4" id="app">

            <h6 class="tw-mb-4 text-uppercase font-weight-bold" style="letter-spacing: 5px;">
                @yield('heading')
            </h6>

            @yield('content')

        </div>

        @include('layouts.footer')

    </div>

    @stack('scriptsBeforeJS')

    <script src="{{ mix('/js/app.js') }}"></script>

    @stack('scripts')

</body>
</html>
