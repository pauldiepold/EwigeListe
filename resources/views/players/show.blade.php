@extends('layout')

@section('title', 'Spielerprofil')

@section('heading')
    Spielerprofil von {{ $player->surname }} {{ $player->name }}
@endsection

@section('content')

    Punkte:

@endsection