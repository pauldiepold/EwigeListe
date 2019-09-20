@extends('layouts.main')

@section('title', 'Gruppe - ' . $group->name)

@section('heading', 'Gruppe - ' . $group->name)

@section('content')
    <p class="font-weight-bold">Mitglieder in dieser Gruppe:</p>

    <div class="flex">
        @forelse ($group->players as $player)
                <p><a href="{{ $player->path() }}">{{ $player->surname }} {{ $player->name }}</a></p>
            </div>
        @empty
            Diese Gruppe hat noch keine Mitglieder.
        @endforelse
    </div>
@endsection
