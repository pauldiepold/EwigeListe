@extends('layouts.main')

@section('content')

    <round :round-prop='@json($roundResource)'
           :can-update='@json(auth()->user()->can('update', $round))'
    ></round>

@endsection
