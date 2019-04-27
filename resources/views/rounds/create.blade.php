@extends('layouts.main')

@section('title', 'Runde Starten')

@section('heading', 'Neue Runde starten')

@section('content')


<div class="row align-items-center mx-auto my-4 no-gutters" style="max-width:230px">

            <div class="col-2" style="padding:0px;">
                @if ($numberOfPlayers != 4)
                    <a href="/rounds/create/{{ $numberOfPlayers-1 }}">
                        <i class="fas fa-minus-square fa-2x text-primary"></i>
					</a>
                @endif
            </div>

            <div class="col-8">
				<span class="text-dark" style="font-size:1.15em">
					@for($i=0; $i < $numberOfPlayers; $i++)						 
						<i class="fas fa-user"></i>
					@endfor
				</span>
            </div>
			
            <div class="col-2" style="padding:0px;">
                @if ($numberOfPlayers != 7)
                    <a href="/rounds/create/{{ $numberOfPlayers+1 }}">
                        <i class="fas fa-plus-square fa-2x text-primary"></i>
                    </a>
                @endif
            </div>
        </div>

    @include('include.error')

    <form method="POST" action="/rounds">
        @csrf

        @for ($k = 0; $k < $numberOfPlayers; $k++)
            <div class="form-group mx-auto mt-4" style="max-width:200px;">
                <label for="player{{ $k }}"><b>Spieler {{ $k+1 }}:</b></label>
                <select class="form-control" name="players[{{ $k }}]">
                    @foreach ($players as $player)
                        <option value="{{ $player->id }}" {{ (old("players")[$k] == $player->id || ($loop->index == $k && old("players")[$k] == null) ? "selected":"") }}>
							{{--  {{ (old("players")[$k] == $player->id || ($loop->index == $k && old("players")[$k] == null) ? "selected":"") }} --}}
                            {{ $player->surname }} {{ $player->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endfor

        <button type="submit" class="btn btn-primary mt-3">Neue Runde Starten</button>
    </form>
<div class="mt-4">
<a  data-container="body" data-toggle="popover" data-placement="top"
   data-content="Die Spieler entsprechend ihrer Sitzreihenfolge auswählen. Spieler 1 beginnt als Geber!">
<i class="fas fa-info-circle fa-lg"></i>
</a>
</div>

@endsection

@push('scripts')
<script>
$(function () {
  $('[data-toggle="popover"]').popover();
});
	
$('body').on('click', function (e) {
    $('[data-toggle=popover]').each(function () {
        if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
            $(this).popover('hide');
        }
    });
});
</script>
@endpush