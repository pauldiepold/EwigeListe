<!doctype html>
<html lang="de" class="h-100">
<head>

    @include('layouts.head')

</head>

<body class="text-center position-relative" style="min-height: 100%; padding-bottom: @auth6.5@endauth@guest5@endguestrem;">

    @include('layouts.nav')

    <div class="container mt-3" id="app">

        <h6 class="mb-4 site-title text-uppercase font-weight-bold" style="letter-spacing: 5px;">
            @yield('heading')
        </h6>

        @yield('content')

    </div>

    @include('layouts.footer')

</body>
</html>
