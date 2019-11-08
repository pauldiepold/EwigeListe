@extends('errors::minimal')

@section('title', 'Server nicht erreichbar')
@section('code', '503')
@section('message')
    @if(isset($exception))
        {{ $exception->getMessage() }}
    @else
        Server ist nicht erreichbar
    @endif
@endsection
