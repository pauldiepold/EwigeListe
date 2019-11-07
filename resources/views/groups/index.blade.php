@extends('layouts.main')

@section('title', 'Listen')

@section('heading', 'Listen')

@section('content')


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
                    Spieler: {{ $group->players_count }}
                </div>
                <div class="tw-mx-2">
                    Runden: {{ $group->rounds_count }}
                </div>
            </div>
        @endforeach
    </div>

    <a href="{{ route('groups.create') }}" class="tw-my-6 btn btn-primary">Neue Gruppe erstellen</a>

    @auth
        @if(auth()->user()->isAdmin())
            <br><br>
            <a class="btn btn-primary tw-my-6 tw-block" href="/listen/calculate">Alle Gruppen aktualisieren</a>
            <a class="btn btn-primary tw-my-6 tw-block" href="/players/calculate">Alle Spieler-Profile aktualisieren</a>
        @endif
    @endauth

@endsection
