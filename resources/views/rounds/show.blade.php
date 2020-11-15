@extends('layouts.main')

@if($isCurrentRound)
    @section('title', 'Aktuelle Runde')
@section('heading', 'Aktuelle Runde')
@else
    @section('title', 'Rundenübersicht')
@section('heading', 'Rundenübersicht')
@endif

@section('content')

    <round :round-prop='@json($round)'
           :can-update='@json(auth()->user()->can('update', $round) ? 'true' : 'false')'
    ></round>

@endsection
