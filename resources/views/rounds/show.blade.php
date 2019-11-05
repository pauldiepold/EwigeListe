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
            @can('update', $round)
                @include('games.create')
            @endcan

            @include('rounds.inc.pointsTable')

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

            @if ($round->games->count() != 0)
                <p class="text-muted tw-text-sm tw-mt-6">
                    Anzahl Spiele: {{ $round->games->count() }}
                </p>
            @endif
        </tab>

        <tab name="Statistiken" icon="fa-chart-area" :selected="false">
            <template v-slot:default="props">
                @if($round->games->count() >= 4)
                    <round-graph :round_id="{{ $round->id }}" :key="props.tabKey"></round-graph>
                @else
                    <p>
                        Das Rundenverlaufs-Diagramm wird ab dem 4. Spiel angezeigt!
                    </p>
                @endif

                @include('rounds.inc.info')
            </template>
        </tab>

        <tab name="Listen" icon="fa-list-alt">
            <template v-slot:default="props">

                <update-groups :round-input="{{ $round }}"
                               :can-update="{{ auth()->user()->can('update', $round) ? 'true' : 'false'}}"
                               :key="props.tabKey"></update-groups>

                @can('update', $round)
                    <a data-container="body" data-toggle="popover" data-placement="top" title="Listen hinzufügen"
                       data-content="Eine Runde kann nur Listen hinzugefügt werden, in denen mindestens einer der Spieler Mitglied ist. Erstelle eine neue Liste oder trete einer bestehende Liste bei, um die Runde auf die Liste zu schreiben.">
                        <i class="fas fa-info-circle fa-lg tw-mt-6"></i>
                    </a>
                @endcan
                {{--
                <div class="mx-auto">
                    <h4>Listen</h4>
                    @forelse($round->groups as $group)
                        <div
                            class="rounded text-left bg-white px-3 py-2 my-3 mx-auto d-flex align-items-center justify-content-between shadow-2"
                            style="max-width: 24rem;">
                            <a href="{{ $group->path() }}" class="font-weight-bold tw-text-black">
                                {{ $group->name }}
                            </a>
                        </div>
                    @empty
                        <p>Diese Runde ist in keiner Liste.</p>
                    @endforelse
                </div>--}}
            </template>
        </tab>

        <tab name="Kommentare" icon="fa-comments">
            @include('comments.index', ['comments' => $round->comments()->oldest()->paginate(8, ['*'], 'comments'), 'route' => 'round'])
        </tab>
    </tabs>
@endsection
