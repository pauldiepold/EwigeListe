@extends('layouts.main')

@section('title', 'Ewige Liste')

@section('heading', 'Startseite')

@section('content')

    @auth
        @if($currentRounds->count() > 0)
            <h4 class="mb-3">Letzte Runden:</h4>
            <div class="mb-5">
                @foreach($currentRounds as $round)
                    <div class="card mb-3 mx-auto" style="max-width: 24rem;">
                        <div class="card-header pb-2 font-weight-bold">
                            {{ niceCount($round->players->pluck('surname'), ' - ') }}
                        </div>
                        <div class="card-body p-2">
                            <span class="text-muted" style="font-size: 0.8rem;">
                                <a href="{{ $round->path() }}"><i class="fas fa-eye fa-2x text-dark"></i></a><br>
                                Letztes Spiel: {{ printDate($round->games->last()->created_at) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif


        @if($comments->count() != 0)
            <h4 class="mt-4 mb-3" id="comments">Neue Kommentare</h4>
            @include('comments.latest', ['comments' => $comments])
        @endif
    @endauth

    <h4 class="tw-my-4">Rekorde:</h4>
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-9 col-lg-7 col-xl-6">
            <table class="table table-sm table-borderless text-left">
                @foreach($group->records as $row)
                    @php $row = collect($row); @endphp
                    <tr>
                        <td class="tw-mb-4">
                            {!! $row->shift() !!}
                        </td>
                        <td>
                            <b>{{ $row->shift() }}</b>
                        </td>
                        <td>
                            {!! $row->shift() !!}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>

    <h4 class="tw-mt-8 tw-mb-4">Statistiken:</h4>
    <div class="row justify-content-center">
        <div class="col-sm-8 col-md-7 col-lg-5 col-xl-4">
            <table class="table table-sm table-borderless text-left">
                @foreach($group->stats as $row)
                    @php $row = collect($row); @endphp
                    <tr>
                        <td{!! $row->contains('margin') ? ' class="pb-3"' : '' !!}>
                            {!! $row->shift() !!}
                        </td>
                        <td>
                            <b>{{ $row->shift() }}</b>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>

    <h4 class="tw-mt-8">Anzahl der Spiele:</h4>
    <group-graph :group_id="{{ $group->id }}"></group-graph>

@endsection
