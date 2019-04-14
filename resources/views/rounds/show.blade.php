@extends('layouts.main')

@section('title', 'Aktuelle Runde')

@section('heading', 'Aktuelle Runde')

@section('content')

    @can('update', $round)
        @include('games.create')
    @endcan

    @include('rounds.inc.pointsTable')

    @can('update', $round)
            @include('games.update')
            <br><br>
        @include('games.delete')
    @endcan

@endsection