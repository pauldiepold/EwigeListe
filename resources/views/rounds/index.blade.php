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
