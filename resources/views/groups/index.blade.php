@extends('layouts.main')

@section('title', 'Gruppen')

@section('heading', 'Gruppen')

@section('content')

    <a href="/groups/create" class="mb-4 btn btn-primary">Neue Gruppe erstellen</a>

    <div class="">
        @foreach ($groups as $group)
            <div
                class="group text-left d-flex align-items-center justify-content-between"
                style="max-width: 30rem;">
                <div class="tw-flex-1">
                    <a href="{{ $group->path() }}" class="font-weight-bold tw-text-black">
                        {{ $group->name }}
                    </a>
                </div>
                <div class="tw-mx-2">
                    Spieler: {{ $group->players->count() }}
                </div>
                <div class="tw-mx-2">
                    Runden: {{ $group->rounds->count() }}
                </div>
            </div>
        @endforeach
    </div>

@endsection
