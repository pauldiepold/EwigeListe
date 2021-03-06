<button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#updateModal">
    Letztes Spiel ändern
</button>

<div class="modal" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mx-auto" id="updateModalLabel">Letztes Spiel ändern</h5>
            </div>
            <div class="modal-body">

                @include('include.errorNamed', ['nameBag' => 'update'])

                <form method="POST" action="/games/{{ $lastGame->id }}">
                    @csrf
                    @method('PATCH')

                    @foreach ($lastGame->players as $player)
                        <div class="custom-control custom-checkbox my-1">
                            <input class="custom-control-input" type="checkbox" value="{{ $player->id }}"
                                   id="updatePlayer{{ $player->id }}" name="updateWinners[]"
                                    {{ $player->pivot->won ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold"
                                   for="updatePlayer{{ $player->id }}">
                                {{ $player->surname }} {{ $player->name }}
                            </label>
                        </div>
                    @endforeach

                    <div class="form-row my-4 mx-auto justify-content-center">
                        <div class="col-xs-6 col-xs-offset-3">
                            <input class="form-control{{ $errors->update->first('points') ? ' is-invalid' : '' }}"
                                   type="number" min="-4" max="16" name="updatePoints"
                                   value="{{ $lastGame->points }}">
                            <label for="updatePoints" class="control-label font-weight-bold">Punkte</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Bestätigen</button>
                    <hr>
                    <div class="custom-control custom-checkbox my-1">
                        <input class="custom-control-input" type="checkbox" value="1" id="updateMisplayed"
                               name="updateMisplayed"
                                {{ $lastGame->misplay ? 'checked' : '' }}>
                        <label class="custom-control-label font-weight-bold" for="updateMisplayed">
                            Falsch bedient?
                        </label>
                    </div>
                    <a data-container="body" data-toggle="popover" data-placement="top" title="Falsch bedient?"
                       data-content="Falls jemand falsch bedient, wird dies als verlorenes Solo mit 2 Punkten plus die getätigten Ansagen gewertet. Dieses Ergebnis wird oben eintragen.">
                        <i class="fas fa-info-circle fa-lg"></i>
                    </a>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary mx-auto" data-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>

@if(count($errors->update) > 0)
    @push('scripts')
        <script>
            $(function () {
                $('#updateModal').modal({show: true});
            });
        </script>
    @endpush
@endif