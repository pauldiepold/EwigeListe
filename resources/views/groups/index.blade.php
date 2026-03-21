{{-- Legacy: Index unter Inertia `Groups/Index.vue`; wird nicht mehr gerendert. --}}
@extends('layouts.main')

@section('title', 'Listen')

@section('heading', 'Listen')

@section('content')

    <div class="">
        @foreach ($groups as $group)
            <a href="{{ $group->path() }}" class="text-black">
                <div
                    class="group text-left d-flex align-items-center justify-content-between"
                    style="max-width: 30rem;">
                    <div class="flex-1 font-bold">
                        {{ $group->name }}
                    </div>
                    @if($group->closed)
                        <div class="mx-2">
                            <i class="fa fa-lock"></i>
                        </div>
                    @endif
                    <div class="mx-2">
                            Personen: {{ $group->players_count }}
                    </div>
                    <div class="mx-2">
                            Runden: {{ $group->rounds_count }}
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <a href="{{ route('groups.create') }}" class="my-6 btn btn-primary">Neue Liste erstellen</a>

@endsection
