@extends('layouts.main')

@if($isCurrentRound)
    @section('title', 'Aktuelle Runde')
@section('heading', 'Aktuelle Runde')
@else
    @section('title', 'Rundenübersicht')
@section('heading', 'Rundenübersicht')
@endif

@section('content')

    @can('update', $round)
        @include('games.create')
    @endcan

    @include('rounds.inc.pointsTable')

    @can('update', $round)
        @if ($round->games->count() != 0)
            <div class="d-flex flex-sm-row flex-column justify-content-center">
                <div class="mx-2 mt-3">
                    @include('games.update')
                </div>
                <div class="mx-2 mt-3">
                    @include('games.delete')
                </div>
            </div>
        @else
            <form method="POST" action="/rounds/{{ $round->id }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-primary mt-4">Runde löschen</button>
            </form>
        @endif
    @endcan

    @if($round->games->count() >= 4)
        <round-graph class="mt-5" :round_id="{{ $round->id }}"></round-graph>
    @endif

    @include('rounds.inc.info')

    <div class="mx-auto mt-5">
        <h4 class="mb-3" id="comments">Gruppen:</h4>
        @forelse($round->groups->filter(function ($value, $key) { return $value->id != 1; }) as $group)
            <div
                class="rounded text-left bg-white px-3 py-2 my-3 mx-auto d-flex align-items-center justify-content-between shadow-2"
                style="max-width: 24rem;">
                <a href="{{ $group->path() }}" class="font-weight-bold tw-text-black">
                    {{ $group->name }}
                </a>
            </div>
        @empty
            <p>Diese Runde ist in keiner Gruppe.</p>
        @endforelse
    </div>

    @include('comments.index', ['comments' => $round->comments()->oldest()->paginate(8, ['*'], 'comments'), 'route' => 'round'])

@endsection
