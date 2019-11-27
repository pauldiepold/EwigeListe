@extends('errors::minimal')

@section('title', 'Server nicht erreichbar')
@section('code', '503')
@section('message')
    @if($exception->getMessage() != '')
        {{ $exception->getMessage() }}
    @else
        Tolle neue Änderungen! Seite ist gleich wieder da.
    @endif
@endsection
