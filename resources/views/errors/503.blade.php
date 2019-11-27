@extends('errors::minimal')

@section('title', 'Server nicht erreichbar')
@section('code', '503')
@section('message')
    @if(isset($exception->getMessage()))
        {{ $exception->getMessage() }}
    @else
        Tolle neue Änderungen für euch!
    @endif
@endsection
