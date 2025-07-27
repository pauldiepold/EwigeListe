@extends('layouts.main')

@section('title', 'Runde Starten')

@section('heading', 'Neue Runde starten')

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show tw-mb-4" role="alert">
            <i class="fas fa-check-circle tw-mr-2"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <create-round :all-players='@json($allPlayers)' :logged-in-player-id='@json(auth()->user()->player->id)'></create-round>

@endsection
