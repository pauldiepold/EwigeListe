@extends('layouts.main')

@section('title', 'Spielerprofil')

@section('heading')
    Spielerprofil von <br class="d-block d-sm-none">{{ $player->surname }} {{ $player->name }}
@endsection

@section('content')
    @include('include.back')

    @if($player->games->count() > 0)
        <hr>

        <h4 id="lastRounds">Zuletzt gespielte Runden:</h4>

        @include('rounds.inc.archiveTable')

    @endif

    <hr>
    <h4>Gruppen:</h4>
    @forelse($groups as $group)
        <p><a href="{{ $group->path() }}">{{ $group->name }}</a></p>

    @empty
        Der Spiele ist bisher in keiner Gruppe.
    @endforelse


    @if(!$rounds->onFirstPage())
        @push('scripts')
            <script>
                $(document).ready(function () {
                    $('html, body').scrollTop($('#lastRounds').offset().top - 50);
                });
            </script>
        @endpush
    @endif

@endsection
