<!doctype html>
<html lang="de">
<head>

    @include('layouts.head')

</head>

<body class="text-center">

    @include('layouts.nav')

    <div class="container">

        <h3 class="mb-4 site-title">
            @yield('heading')
        </h3>

        @yield('content')

    </div>

@include('layouts.footer')

</body>
</html>