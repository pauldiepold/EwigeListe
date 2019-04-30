@extends('layouts.main')

@if($current)
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
        <div class="d-flex flex-sm-row flex-column justify-content-center">
            <div class="mx-2 mt-3">
                @include('games.update')
            </div>
            <div class="mx-2 mt-3">
                @include('games.delete')
            </div>
        </div>
    @endcan

    @if($round->games->count() >= 4)
        <hr>
        <graph_round :round_id="{{ $round->id }}"></graph_round>
    @endif

    <div class="text-small">
        @include('rounds.inc.info')
    </div>

@endsection