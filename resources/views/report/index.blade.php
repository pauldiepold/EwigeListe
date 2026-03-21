@extends('layouts.main')

@section('title', 'Reporting')

@section('heading', 'Reporting')

@section('content')

    <a class="btn btn-primary my-6 block" href="/listen/calculate">Alle Gruppen aktualisieren</a><br>
    <a class="btn btn-primary my-6 block" href="/players/calculate">Alle User-Profile aktualisieren</a>
    <br>
    /liste/calculate/{group}<br>
    /liste/calculateBadges/{group}<br>
    /players/calculate/{player}<br><br>

@endsection
