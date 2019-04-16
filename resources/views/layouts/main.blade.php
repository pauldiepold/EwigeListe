<!doctype html>
<html lang="de">
<head>

    @include('layouts.head')

</head>

<body class="text-center">

    @include('layouts.nav')

    <div class="container">

        <h5 class="mb-4 site-title text-uppercase">
            @yield('heading')
        </h5>

        @yield('content')

    </div>

@include('layouts.footer')

</body>
</html>
