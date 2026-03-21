{{--
  LEGACY – nicht mehr aktiver Render-Pfad.
  Rundenarchiv-Vollseite wird über RoundController@index als Inertia::render('Rounds/Index') ausgeliefert.
  Diese Datei bleibt bis zum finalen Cleanup erhalten.
  Neuer Code: resources/js/inertia/Pages/Rounds/Index.vue
--}}
@extends('layouts.main')

@section('title', 'Rundenarchiv')

@section('heading', 'Rundenarchiv')

@section('content')

    <select-liste>
        @foreach($groups as $group)
            <option value="{{ route('rounds.index', ['group' =>  $group->id ]) }}"
                {{ $selectedGroup->id == $group->id ? ' selected' : '' }}>
                {{ $group->name }}
            </option>
        @endforeach
    </select-liste>

    @include('rounds.inc.archiveTable')

@endsection
