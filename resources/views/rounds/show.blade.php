@extends('layouts.main')

@if($isCurrentRound)
    @section('title', 'Aktuelle Runde')
@section('heading', 'Aktuelle Runde')
@else
    @section('title', 'Rundenübersicht')
@section('heading', 'Rundenübersicht')
@endif

@section('content')

    <tabs>
        <tab name="Runde" icon="fa-play-circle" :selected="true">
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
        </tab>

        <tab name="Statistiken" icon="fa-chart-area">
            <h4>Statistiken</h4>
            @if($round->games->count() >= 4)
                Hallo
                <round-graph :round_id="{{ $round->id }}"></round-graph>
            @else
                <p>
                    Das Rundenverlaufs-Diagramm wird ab dem 4. Spiel angezeigt!
                </p>
            @endif

            @include('rounds.inc.info')
        </tab>

        <tab name="Listen" icon="fa-list-alt">
            <div class="mx-auto">
                <h4>Listen</h4>
                @forelse($round->groups->filter(function ($value, $key) { return $value->id != 1; }) as $group)
                    <div
                        class="rounded text-left bg-white px-3 py-2 my-3 mx-auto d-flex align-items-center justify-content-between shadow-2"
                        style="max-width: 24rem;">
                        <a href="{{ $group->path() }}" class="font-weight-bold tw-text-black">
                            {{ $group->name }}
                        </a>
                    </div>
                @empty
                    <p>Diese Runde ist in keiner Liste.</p>
                @endforelse
            </div>
        </tab>

        <tab name="Kommentare" icon="fa-comments">
            @include('comments.index', ['comments' => $round->comments()->oldest()->paginate(8, ['*'], 'comments'), 'route' => 'round'])
        </tab>
    </tabs>
@endsection
