<footer class="footer py-3 tw-bg-blue-darker position-absolute w-100" style="bottom: 0">
    <div class="container text-center text-muted">
        @auth
            @php
                $player = Illuminate\Support\Facades\Cache::remember('player' . Auth::id(), 60*60, function () {
                    return auth()->user()->player;
                });
            @endphp
            Angemeldet: <a href="{{ $player->path() }}">{{ $player->surname }} {{ $player->name }}</a> &bull;

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
        {{--&bull; <a href="https://dev.ewige-liste.de/report">Report</a>--}}
        &bull; <a href="/telescope" target="_blank">Telescope</a>
        @endif
        @endauth
    </div>
</footer>
