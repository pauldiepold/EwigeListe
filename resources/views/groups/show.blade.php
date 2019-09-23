@extends('layouts.main')

@section('title', 'Gruppe - ' . $group->name)

@section('heading', 'Gruppe - ' . $group->name)

@section('content')
    <h4>Mitglieder in dieser Gruppe:</h4>

    <div class="flex">
        @forelse ($group->players as $player)
            <p><a href="{{ $player->path() }}">{{ $player->surname }} {{ $player->name }}</a></p>
    </div>
    @empty
        Diese Gruppe hat noch keine Mitglieder.
        @endforelse
        </div>

        <h4 class="tw-mt-10" id="lastRounds">In dieser Gruppe gespielte Runden:</h4>

        @include('rounds.inc.archiveTable')
@endsection
