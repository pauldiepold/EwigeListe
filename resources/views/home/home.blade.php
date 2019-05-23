@extends('layouts.main')

@section('title', 'Ewige Liste')

@section('heading', 'Startseite')

@section('content')

    <p style="font-size: 1.4rem;">Willkommen auf der Ewigen Liste!</p>
    
    @auth
<hr>
        @if($currentRounds->count() > 0)
            <h5 class="mt-4 mb-3 font-weight-bold" style="font-family:Raleway">Momentan aktive Runden:</h5>
            <div class="mb-5">
                @foreach($currentRounds as $round)
                    <div class="card my-2 mx-auto" style="max-width: 22rem;">
                        <div class="card-header pb-2">
                            <span class="text-large"
                                  style="font-size: 1rem;">{{ niceCount($round->players->pluck('surname'), ' - ') }}</span>
                        </div>
                        <div class="card-body p-2">
<span class="" style="font-size: 0.8rem;">
	<a href="/rounds/{{ $round->id }}"><i class="fas fa-eye fa-2x text-dark"></i></a><br>
	Letztes Spiel eingetragen {{ printDate($round->games()->latest()->first()->created_at) }}
</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif


@if($comments->count() != 0)
<hr>
    @include('comments.latest', ['comments' => $comments])
@endif
    @endauth

<hr>

    <h5 class="mb-3 mt-4 font-weight-bold" style="font-family:Raleway">Rekorde:</h5>
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-9 col-lg-7 col-xl-6">
            <table class="table table-sm table-borderless text-left">
                @foreach($colFP as $row)
                    <tr>
                        <td{!! $row->contains('margin') ? ' class="pb-3"' : '' !!}>
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

    <h5 class="my-3 font-weight-bold" style="font-family:Raleway">Statistiken:</h5>
    <div class="row justify-content-center">
        <div class="col-sm-8 col-md-7 col-lg-5 col-xl-4">
            <table class="table table-sm table-borderless text-left">
                @foreach($colStats as $row)
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

    <hr>
    <h5 class="my-3 font-weight-bold" style="font-family:Raleway">Anzahl der Spiele:</h5>
    <home-graph></home-graph>

@endsection