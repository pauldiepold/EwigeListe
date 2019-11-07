@extends('layouts.main')

@section('title', 'Runde Starten')

@section('heading', 'Neue Runde starten')

@section('content')

    <create-round :all-players="{{ $allPlayers }}" :logged-in-player-id="{{ auth()->user()->player->id }}"></create-round>

@endsection
