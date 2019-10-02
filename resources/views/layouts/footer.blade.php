<footer class="footer py-3 bg-dark position-absolute w-100" style="bottom: 0">
    <div class="container text-center">
        @auth
            <span class="text-muted">
            @php
                $player = Illuminate\Support\Facades\Cache::remember('player' . Auth::id(), 60*60, function () {
                    return auth()->user()->player;
                });
            @endphp
            Angemeldet: <a href="{{ $player->path() }}">{{ $player->surname }} {{ $player->name }}</a> &bull;
        </span>

            <a href="{{ route('logout') }}"
               onclick="event.preventDefault();
               document.getElementById('logout-form').submit();">
                Logout
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                  style="display: none;">
                @csrf
            </form>

            @if(Auth::user()->player->id == 1)
                <br><a href="https://dev.ewige-liste.de/report">Admin Tools</a> <span class="text-muted">&bull;</span> <a href="/telescope">Telescope</a>
            @endif

        @endauth
            @guest
                <a href="/login">Login</a>
            @endguest
        <br>

        <span class="text-muted">&copy; Paul Diepold &bull; {{ now()->year }}</span>
    </div>
</footer>
<script>
	let preselectedPlayers = 0;
</script>
@stack('scriptsBeforeJS')
<script src="{{ mix('/js/app.js') }}"></script>
@stack('scripts')
