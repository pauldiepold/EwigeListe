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
        <hr>
        <round-graph :round_id="{{ $round->id }}"></round-graph>
    @endif


    @include('rounds.inc.info')

@endsection