@extends('layouts.main')

@section('title', 'Runde Starten')

@section('heading', 'Neue Runde starten')

@section('content')

    @if (session('success'))
        <div x-data="{ visible: true }" x-show="visible" class="mb-4">
            <div class="flex items-center justify-between px-4 py-3 rounded border bg-green-100 text-green-800 border-green-300">
                <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
                <button @click="visible = false" class="ml-4 text-green-600 hover:text-green-900" aria-label="Schließen">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    @endif

    <create-round :all-players='@json($allPlayers)' :logged-in-player-id='@json(auth()->user()->player->id)'></create-round>

@endsection
