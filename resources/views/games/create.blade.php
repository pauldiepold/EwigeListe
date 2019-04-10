@extends('layout')

@section('title', 'Spiel eingeben')

@section('heading', 'Spiel eingeben')

@section('content')
    <p>Welche Spieler haben gewonnen?</p>

    <form method="POST" action="/rounds/{{ $round->id }}/game">
        @csrf

        @foreach ($players as $player)
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
                <input class="form-control" type="number" min="-4" max="16" value="0" name="points">
                <label for="points" class="control-label font-weight-bold">Punkte</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Bestätigen</button>
    </form>
    <hr>
    <a class="btn btn-primary" href="{{ URL::previous() }}">Zurück</a>
@endsection