@extends('layouts.main')

@section('title', 'Rundenarchiv')

@section('heading', 'Rundenarchiv')

@section('content')

    @include('rounds.inc.archiveTable', ['profile' => 0])

@endsection