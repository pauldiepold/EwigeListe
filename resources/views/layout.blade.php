<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css"
          integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link href="{{ asset('css/style.css') }}?v={{ time() }}" rel="stylesheet" type="text/css" >

    <title>@yield('title')</title>
</head>
<body style="margin-top: 4em;" class="text-center">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <button class="navbar-toggler mx-auto" type="button" data-toggle="collapse" data-target="#navbarCollapse"
                aria-controls="navbarsExample08" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span><span style="vertical-align: middle;"> Menü</span>
        </button>

        <div class="collapse navbar-collapse justify-content-md-center" id="navbarCollapse">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/rounds/create">Neue Runde starten</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/players">Ewige Liste</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/rounds">Rundenarchiv</a>
                </li>
            </ul>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
            integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
            integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
            crossorigin="anonymous"></script>
</body>
</html>