<button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#deleteModal">
    Letztes Spiel löschen
</button>

<div class="modal" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mx-auto" id="deleteModalLabel">Willst du dieses Spiel wirklich
                    löschen?</h5>
            </div>
            <div class="modal-body">
                @foreach ($lastGame->players as $player)
                    <p{!! $player->pivot->won ? ' class="font-weight-bold"' : '' !!}>{{ $player->surname }} {{ $player->name }}
                        : {{ $player->pivot->points }}</p>
                @endforeach

                <form method="POST" action="/games/{{ $lastGame->id }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-primary mt-3">Spiel Löschen</button>
                </form>
                @include('include.error')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary mx-auto" data-dismiss="modal">Schließen
                </button>
            </div>
        </div>
    </div>
</div>