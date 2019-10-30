<div class="py-2 tw-bg-blue-dark">
    <a href="/" class="link-unstyled">
        <h3 class="site-title text-light mb-0">Ewige Liste</h3>
    </a>
</div>

<nav class="tw-z-50 tw-bg-blue" id="navbar">
    <div class="md:tw-max-w-md tw-px-2 tw-flex tw-justify-around tw-mx-auto">
        <a href="/">
            <nav-icon icon="fa-home"></nav-icon>
        </a>
        @auth
            @if(Auth::user()->player->rounds->count() != 0)
                <a href="{{ route('rounds.current') }}">
                    <nav-icon icon="fa-play-circle"></nav-icon>
                </a>
            @endif
            <a href="{{ route('rounds.create') }}">
                <nav-icon icon="fa-plus-circle"></nav-icon>
            </a>
        @endauth
        <a href="{{ route('groups.index') }}">
            <nav-icon icon="fa-list-alt"></nav-icon>
        </a>
        @auth
            <a href="{{ route('rounds.index') }}">
                <nav-icon icon="fa-history"></nav-icon>
            </a>
        @endauth
        @guest
            <a href="{{ route('rounds.index') }}">
                <nav-icon icon="fa-sign-in-alt"></nav-icon>
            </a>
            <a href="{{ route('rounds.index') }}">
                <nav-icon icon="fa-user-plus"></nav-icon>
            </a>
        @endguest
    </div>
</nav>

{{--
<nav class="navbar navbar-expand-lg navbar-dark bg-secondary sticky-top" id="navbar">
    <button class="navbar-toggler mx-auto" id="navbar-toggler" type="button" data-toggle="collapse"
            data-target="#navbarCollapse"
            aria-controls="navbarsExample08" aria-expanded="false" aria-label="Toggle navigation">
        <i id="fa-bars" class="fas fa-bars"></i>
        <i id="fa-times" class="fas fa-times" style="display:none;"></i>
    </button>

    <div class="collapse navbar-collapse justify-content-md-center text-light" id="navbarCollapse">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="/home">Startseite</a>
            </li>
            @auth
                @if(Auth::user()->player->rounds->count() != 0)
                    <li class="nav-item">
                        <a class="nav-link" href="/rounds/current">Aktuelle Runde</a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('rounds.create') }}">Neue Runde</a>
                </li>
            @endauth
             <li class="nav-item">
                <a class="nav-link" href="{{ route('ewigeListe') }}">Ewige Liste</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('groups.index') }}">Listen</a>
            </li>
            @guest
                <li class="nav-item">
                    <a class="nav-link" href="/login">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/register">Registrieren</a>
                </li>
            @endguest
            @auth
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('rounds.index') }}">Archiv</a>
                </li>
            @endauth
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Sonstiges
                </a>

                <div class="dropdown-menu bg-secondary text-light">
                    <a href="/regeln" class="dropdown-item">Regeln</a>
                    <a href="/impressum" class="dropdown-item">Impressum</a>
                    <a href="/datenschutz" class="dropdown-item">Datenschutz</a>
                </div>
            </li>
        </ul>
    </div>
</nav>
--}}
