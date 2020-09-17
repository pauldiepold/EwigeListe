@extends('layouts.main')

@if($isCurrentRound)
    @section('title', 'Aktuelle Runde')
@section('heading', 'Aktuelle Runde')
@else
    @section('title', 'Rundenübersicht')
@section('heading', 'Rundenübersicht')
@endif

@section('content')
    <tabs>
        <tab name="Runde" icon="fa-play-circle" :selected="true">
            @if(true)<!--!$round->live_round_id)-->
                @can('update', $round)
                    @include('games.create')
                @endcan
            @endif

            @include('rounds.inc.pointsTable')

            @if(true)<!--!$round->live_round_id)-->
                @can('update', $round)
                    @if ($round->games->count() != 0)
                        <div class="d-flex flex-sm-row flex-column justify-content-center">
                            <div class="mx-2 mt-3">
                                @include('games.update')
                            </div>
                            <div class="mx-2 mt-3">
                                @include('games.delete')
                            </div>
                        </div>
                    @else
                        <form method="POST" action="{{ route('rounds.destroy', ['round' => $round->id]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-primary mt-4">Runde löschen</button>
                        </form>
                    @endif
                @endcan
            @endif

            @include('rounds.inc.info')
        </tab>

        @can('update', $liveRound)
            @if($round->live_round_id)
                @push('scriptsHead')
                    <link rel="stylesheet" href="{{ mix('/css/cards.css') }}">
                @endpush
                <tab name="Live" icon="fa-dice" :selected="false">
                    <template v-slot:default="props">
                        <live-game :round-players-ids='@json($round->players->pluck('id'))'
                                   :live-round-id='@json($liveRound->id)'
                                   :auth-id='@json(auth()->id())'
                                   @isset($liveGame)
                                   :live-game-init='@json($liveGame)'
                                   :ich-init='@json($liveGame->getSpieler(auth()->id()))'
                                @endisset
                        ></live-game>
                    </template>
                </tab>
            @endif
        @endcan

        <tab name="Statistiken" icon="fa-chart-area" :selected="false">
            <template v-slot:default="props">
                @if($round->games->count() >= 4)
                    <round-graph :round_id="{{ $round->id }}" :key="props.tabKey"></round-graph>
                @else
                    <h5 class="tw-mb-8">Das Rundenverlaufs-Diagramm wird ab dem 4. Spiel angezeigt!</h5>
                @endif

                @include('rounds.inc.info')
            </template>
        </tab>

        <tab name="Listen" icon="fa-list-alt">
            <template v-slot:default="props">
                <update-groups :round-input='@json($round)'
                               :can-update='@json(auth()->user()->can('update', $round) ? 'true' : 'false')'
                               :key="props.tabKey"></update-groups>
            </template>
        </tab>

        <tab name="Kommentare" icon="fa-comments">
            @include('comments.index', ['comments' => $round->comments()->oldest()->paginate(8, ['*'], 'comments'), 'route' => 'round'])
            @if(Auth::id() == 1)
                <hr>
                <template v-slot:default="props">
                    <update-round-dates :round-id="{{ $round->id }}" :key="props.tabKey"></update-round-dates>
                </template>
            @endif
        </tab>


    </tabs>
@endsection
