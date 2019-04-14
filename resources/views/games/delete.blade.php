@if ($round->games->count() != 0)
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#deleteModal">
        Letztes Spiel löschen
    </button>

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mx-auto" id="deleteModalLabel">Willst du dieses Spiel wirklich
                        löschen?</h5>
                </div>
                <div class="modal-body">
                    @foreach ($lastGame->players as $player)
                        <p>{{ $player->surname }} {{ $player->name }}
                            : {{ $lastGame->players()->where('player_id', $player->id)->first()->pivot->points }}</p>
                    @endforeach

                    <form method="POST" action="/games/{{ $lastGame->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-primary mt-3">Spiel Löschen</button>
                    </form>
                    @include('include.error')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary mx-auto" data-dismiss="modal">Schließen
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if (count($errors) > 0)
        <script type="text/javascript">
            $(document).ready(function () {
                $('#deleteModal').modal({show: true});
            });
        </script>
    @endif
@endif