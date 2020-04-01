@extends('layouts.main')

@section('title', 'Runde Starten')

@section('heading', 'Neue Runde starten')

@section('content')

    <create-round :all-players='@json($allPlayers)' :logged-in-player-id='@json(auth()->user()->player->id)'></create-round>

@endsection
