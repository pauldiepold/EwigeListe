<footer class="footer py-3 mt-4 bg-dark">
    <div class="container text-center">
        @auth
        <span class="text-muted">
            @php
			$player = Illuminate\Support\Facades\Cache::remember('player' . Auth::id(), 60*60, function () {
				return Auth::user()->player;
			});
			@endphp
            Angemeldet: <a href="/profiles/{{ $player->id }}">{{ $player->surname }} {{ $player->name }}</a> &bull;
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
        @endauth
        @guest
            <a href="/login">Login</a>
        @endguest
        <br>

        <span class="text-muted">&copy; Paul Diepold &bull; {{ now()->year }}</span>
    </div>
</footer>

<script src="{{ mix('/js/app.js') }}"></script>
@stack('scripts')