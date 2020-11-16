@extends('layouts.main')

@if($isCurrentRound)
    @section('title', 'Aktuelle Runde')
@section('heading', 'Aktuelle Runde')
@else
    @section('title', 'Rundenübersicht')
@section('heading', 'Rundenübersicht')
@endif

@section('content')

    <round :round-prop='@json($roundResource)'
           :auth-id='@json(auth()->id())'
           :can-update='@json(auth()->user()->can('update', $round))'
    ></round>

@endsection
