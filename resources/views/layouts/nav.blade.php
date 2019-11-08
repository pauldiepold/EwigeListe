<div class="py-2 tw-bg-blue-dark">
    <a href="/" class="link-unstyled">
        <h3 class="site-title text-light mb-0">Ewige Liste</h3>
    </a>
</div>

<nav class="tw-bg-blue tw-sticky tw-top-0 tw-z-50">
    <div class="sm:tw-max-w-md tw-px-2 tw-flex tw-justify-around tw-mx-auto">
        <a href="/">
            <nav-icon icon="fa-home" tooltip="Startseite"></nav-icon>
        </a>
        @auth
            @if(Auth::user()->player->rounds->count() != 0)
                <a href="{{ route('rounds.current') }}">
                    <nav-icon icon="fa-play-circle" tooltip="Aktuelle Runde"></nav-icon>
                </a>
            @endif
            <a href="{{ route('rounds.create') }}">
                <nav-icon icon="fa-plus-circle" tooltip="Neue Runde"></nav-icon>
            </a>
        @endauth
        <a href="{{ route('groups.index') }}">
            <nav-icon icon="fa-list-alt" tooltip="Listen"></nav-icon>
        </a>
        @auth
            <a href="{{ route('rounds.index') }}">
                <nav-icon icon="fa-history" tooltip="Rundenarchiv"></nav-icon>
            </a>
        @endauth
        @guest
            <a href="{{ route('login') }}">
                <nav-icon icon="fa-sign-in-alt" tooltip="Login"></nav-icon>
            </a>
            <a href="{{ route('register') }}">
                <nav-icon icon="fa-user-plus" tooltip="Registrieren"></nav-icon>
            </a>
        @endguest
    </div>
</nav>
