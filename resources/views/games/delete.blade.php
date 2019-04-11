@extends('layout')

@section('title', 'Spiel löschen')

@section('heading', 'Spiel löschen')

@section('content')
    <p></p>

    @foreach ($game->players as $player)
        <p>{{ $player->surname }} {{ $player->name }}: {{ $game->players()->where('player_id', $player->id)->first()->pivot->points }}</p>
    @endforeach

    <form method="POST" action="/games/{{ $game->id }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-primary">Spiel Löschen</button>
    </form>
    @include('error')
    <hr>
    <a class="btn btn-primary" href="{{ URL::previous() }}">Zurück zur Übersicht</a>
@endsection