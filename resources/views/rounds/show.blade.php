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
        @include('games.update')
        <br><br>
        @include('games.delete')
    @endcan

@endsection