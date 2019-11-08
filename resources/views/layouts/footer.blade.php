<footer class="footer py-3 tw-bg-blue-darker position-absolute w-100" style="bottom: 0">
    <div class="container text-center text-muted">
        @auth
            Angemeldet: <a href="{{ auth()->user()->player->path() }}">{{ auth()->user()->player->surname }} {{ auth()->user()->player->name }}</a> &bull;

            <a href="{{ route('logout') }}"
               onclick="event.preventDefault();
               document.getElementById('logout-form').submit();">
                Logout
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                  style="display: none;">
                @csrf
            </form>


        @endauth

        @guest
            <a href="{{ route('login') }}">Login</a>
        @endguest

        <br>
        <a href="{{ route('regeln') }}">Regeln</a>
        &bull;
        <a href="{{ route('impressum') }}">Impressum</a>
        &bull;
        <a href="{{ route('datenschutz') }}">Datenschutz</a>
        <br>

        <span class="text-muted">&copy; Paul Diepold &bull; {{ now()->year }}</span>

        @auth
        @if(Auth::user()->player->id == 1)
        &bull; <a href="/report">Admin</a>
        &bull; <a href="/telescope" target="_blank">Telescope</a>
        @endif
        @endauth
    </div>
</footer>
