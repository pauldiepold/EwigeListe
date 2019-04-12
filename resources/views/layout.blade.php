<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
    <script src="{{ mix('/js/app.js') }}"></script>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css"
          integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

    <title>@yield('title')</title>
</head>
<body class="text-center">
    <script>
        $(function () {
            $('#navbarCollapse').on('show.bs.collapse', function () {
                $('#fa-bars').hide();
                $('#fa-times').show();
            });
            $('#navbarCollapse').on('hide.bs.collapse', function () {
                $('#fa-bars').show();
                $('#fa-times').hide();
            });
        });
    </script>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="row w-100 justify-content-center mx-auto">
            <div class="col text-center text-light">
                <h3>Ewige Liste</h3>
            </div>
            <div class="w-100"></div>
            <div class="col">
                <button class="navbar-toggler mx-auto" type="button" data-toggle="collapse"
                        data-target="#navbarCollapse"
                        aria-controls="navbarsExample08" aria-expanded="false" aria-label="Toggle navigation">
                    <i id="fa-bars" class="fas fa-bars"></i>
                    <i id="fa-times" class="fas fa-times" style="display:none;"></i>
                </button>

                <div class="collapse navbar-collapse justify-content-md-center" id="navbarCollapse">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="/players">Ewige Liste</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/rounds/create">Neue Runde starten</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/rounds">Rundenarchiv</a>
                        </li>

                        <li class="nav-item">
                            @guest
                                <a class="nav-link" href="/login">Login</a>
                            @endguest
                            @auth
                                <a class="nav-link" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                            @endauth
                        </li>

                    </ul>
                </div>
            </div>
        </div>


    </nav>
    <div class="container">
        <h3 class="mb-4">
            @yield('heading')
        </h3>
        @yield('content')
    </div>

    <footer class="footer py-3 mt-4 bg-dark">
        <div class="container text-center">
            <span class="text-muted">&copy; Paul Diepold {{ now()->year }}</span>
        </div>
    </footer>

</body>
</html>