@extends('layouts.main')

@section('title', 'Rundenarchiv')

@section('heading', 'Rundenarchiv')

@section('content')

    <h5>Liste:</h5>
    <select class="form-control form-control-sm tw-max-w-xs tw-mx-auto tw-mb-8" name="group" id="group"
            onchange="location = this.value">
        @foreach($groups as $group)
            <option value="{{ route('rounds.index', ['group' =>  $group->id ]) }}"
                {{ $selectedGroup->id == $group->id ? ' selected' : '' }}>
                {{ $group->name }}
            </option>
        @endforeach
    </select>

    @include('rounds.inc.archiveTable')

@endsection
