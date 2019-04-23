<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal">
    Spiel eintragen
</button>

<div class="modal" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mx-auto" id="createModalLabel">Welche Spieler haben gewonnen?</h5>
            </div>
            <div class="modal-body">

                @include('include.errorNamed', ['nameBag' => 'create'])

                <form method="POST" action="/rounds/{{ $round->id }}/game">
                    @csrf

                    @foreach ($activePlayers as $player)
                        <div class="custom-control custom-checkbox my-1">
                            <input class="custom-control-input" type="checkbox" value="{{ $player->id }}"
                                   id="player{{ $player->id }}" name="winners[]"
                                    {{ collect(old('winners'))->contains($player->id) ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold" for="player{{ $player->id }}">
                                {{ $player->surname }} {{ $player->name }}
                            </label>
                        </div>
                    @endforeach

                    <div class="form-row my-4 mx-auto justify-content-center">
                        <div class="col-xs-6 col-xs-offset-3">
                            <input class="form-control{{ $errors->create->first('points') ? ' is-invalid' : '' }}"
                                   type="number" min="-4" max="16" name="points" value="{{ old('points') }}">
                            <label for="points" class="control-label font-weight-bold">Punkte</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Bestätigen</button>
					<hr>
					<div class="custom-control custom-checkbox my-1">
					<input class="custom-control-input" type="checkbox" value="1" id="misplayed" name="misplayed"
						   {{ old('misplayed') ? 'checked' : '' }}>
					<label class="custom-control-label font-weight-bold" for="misplayed">
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
                <button type="button" class="btn btn-secondary mx-auto" data-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>

@if(count($errors->create) > 0)
    @push('scripts')
        <script>
            $(function () {
                $('#createModal').modal({show: true});
            });
        </script>
    @endpush
@endif

@push('scripts')
<script>
$(function () {
  $('[data-toggle="popover"]').popover();
});
</script>
@endpush

@push('scripts')
<script>
$('body').on('click', function (e) {
    $('[data-toggle=popover]').each(function () {
        // hide any open popovers when the anywhere else in the body is clicked
        if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
            $(this).popover('hide');
        }
    });
});
</script>
@endpush