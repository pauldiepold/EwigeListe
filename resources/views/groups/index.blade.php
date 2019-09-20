@extends('layouts.main')

@section('title', 'Gruppen')

@section('heading', 'Gruppen')

@section('content')

    <a href="/groups/create" class="mb-4 btn btn-primary">Neue Gruppe erstellen</a>

    <div class="tw-flex">
        @foreach ($groups as $group)
            <div class="tw-bg-white tw-mr-4 tw-rounded tw-shadow">
                <p><a href="{{ $group->path() }}">{{ $group->name }}</a></p>
            </div>
        @endforeach
    </div>

@endsection
