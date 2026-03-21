<div x-data="{ open: false }">

    <button type="button" class="btn btn-outline-primary" @click="open = true">
        Letztes Spiel löschen
    </button>

    <div x-show="open"
         x-transition.opacity
         @keydown.escape.window="open = false"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
         style="display: none;">
        <div class="bg-white rounded w-full max-w-lg mx-4 shadow-xl">
            <div class="modal-header">
                <h5 class="modal-title mx-auto">Willst du dieses Spiel wirklich löschen?</h5>
            </div>
            <div class="modal-body">
                @foreach ($lastGame->players as $player)
                    <p{!! $player->pivot->won ? ' class="font-bold"' : '' !!}>
                        {{ $player->surname }} {{ $player->name }}: {{ $player->pivot->points }}
                    </p>
                @endforeach

                <form method="POST" action="/games/{{ $lastGame->id }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-primary mt-3">Spiel löschen</button>
                </form>
                @include('include.error')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary mx-auto" @click="open = false">Schließen</button>
            </div>
        </div>
    </div>
</div>
