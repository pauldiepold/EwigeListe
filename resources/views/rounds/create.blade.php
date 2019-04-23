@extends('layouts.main')

@section('title', 'Runde Starten')

@section('heading', 'Neue Runde starten')

@section('content')

<p>Die Spieler entsprechend ihrer Sitzreihenfolge ausw&auml;hlen.</p>

<p><b>Spieler 1</b> beginnt als Geber!</p>

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

        <div class="row align-items-center mx-auto my-4" style="max-width:220px">

            <div class="col-2" style="padding:0px;">
                @if ($numberOfPlayers != 4)
                    <a href="/rounds/create/{{ $numberOfPlayers-1 }}">
                        <i class="fas fa-minus-square fa-2x text-secondary"></i>
					</a>
                @endif
            </div>

            <div class="col-8">
                <span style="font-size:1.15em">Spieleranzahl</span>
            </div>
			
            <div class="col-2" style="padding:0px;">
                @if ($numberOfPlayers != 7)
                    <a href="/rounds/create/{{ $numberOfPlayers+1 }}">
                        <i class="fas fa-plus-square fa-2x text-secondary"></i>
                    </a>
                @endif
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Neue Runde Starten</button>
    </form>

@endsection