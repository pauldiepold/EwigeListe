<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal">
    Spiel eintragen
</button>

<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mx-auto" id="createModalLabel">Welche Spieler haben gewonnen?</h5>
            </div>
            <div class="modal-body">

                <form method="POST" action="/rounds/{{ $round->id }}/game">
                    @csrf

                    @foreach ($activePlayers as $player)
                        <div class="custom-control custom-checkbox my-1">
                            <input class="custom-control-input" type="checkbox" value="{{ $player->id }}"
                                   id="player{{ $player->id }}" name="winners[]">
                            <label class="custom-control-label font-weight-bold" for="player{{ $player->id }}">
                                {{ $player->surname }} {{ $player->name }}
                            </label>
                        </div>
                    @endforeach

                    <div class="form-row my-4 mx-auto justify-content-center">
                        <div class="col-xs-6 col-xs-offset-3">
                            <input class="form-control" type="number" min="-4" max="16" name="points">
                            <label for="points" class="control-label font-weight-bold">Punkte</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Bestätigen</button>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary mx-auto" data-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>