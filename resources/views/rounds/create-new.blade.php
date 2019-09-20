@extends('layouts.main')

@section('title', 'Runde Starten')

@section('heading', 'Neue Runde starten')

@section('content')

<create-round :all-players="{{ json_encode($allPlayers) }}">



</create-round>

@endsection
