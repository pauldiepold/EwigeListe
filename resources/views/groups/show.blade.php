@extends('layouts.main')

@if($group->id == 1)
    @section('heading', $group->name)
@section('title', $group->name)
@else
    @section('heading', 'Liste - ' . $group->name)
@section('title', 'Liste - ' . $group->name)
@endif

@section('content')
    <tabs>
        <tab name="Mitglieder" icon="fa-users" :selected="true">
            @if(!$group->players->contains(auth()->user()->player))
                <a href="{{ route('groups.addPlayer', ['group' => $group->id]) }}" class="btn btn-outline-primary">Liste
                    beitreten</a>
            @endif
            @if($canLeaveGroup)
                <a href="{{ route('groups.leave', ['group' => $group->id]) }}" class="btn btn-outline-primary">Aus Liste
                    austreten</a>
            @endif
            @if($group->rounds->count() >= 1 && $group->rounds->first()->games->count() > 0)
                <div class="row justify-content-center my-4" v-touch:swipe.stop="">
                    <div class="col col-xl-7 col-lg-8 col-md-9">
                        <table class="table myDataTable d-none table-responsive-sm">
                            <thead>
                                <tr class="border-bottom-thick" style="line-height:120%;">
                                    <th>
                                        <span class="tw-text-transparent">Phot</span>
                                    </th>
                                    <th>Name</th>
                                    <th>Spiele</th>
                                    <th>Punkte</th>
                                    <th>Schnitt</th>
                                    <th>Soli</th>
                                </tr>
                            </thead>


                            <tbody>
                                @foreach($group->profiles as $profile)
                                    <tr class="{{ $profile->player->id == auth()->user()->player->id ? ' bg-primary-light' : ''}}">
                                        <td style="padding-right: 0px;">
                                            <a href="{{ $profile->path() }}">
                                                <img src="{{ $profile->player->user->avatar_path }}"
                                                     class="tw-mx-auto tw-h-10 tw-w-10 sm:tw-w-8 sm:tw-h-8 tw-m-0 tw-rounded-full">
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ $profile->path() }}">
                                                {{ $profile->player->surname }} {{ $profile->player->name }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $profile->games }}
                                        </td>
                                        <td>
                                            {{ $profile->points }}
                                        </td>
                                        <td>
                                            {{ $profile->pointsPerGame }}
                                        </td>
                                        <td>
                                            {{ $profile->soli }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p class="mt-4">
                            {{ $group->players->count() }} Mitglieder
                        </p>
                    </div>
                </div>
            @elseif($group->players->count() == 1 && $group->players->first()->id == auth()->user()->player->id)
                <h5>Nur du bist bisher Mitglied dieser Liste.</h5>
            @elseif($group->players->count() == 1)
                <h5 class="tw-mt-8">Bisher ist nur <a
                            href="{{ $group->profiles->first()->path() }}">{{ $group->players->first()->surname }}</a>
                    Mitglied dieser Liste.</h5>
            @else
                <h5 class="tw-mt-8">Bisher wurde keine Runde auf dieser Liste gespielt.</h5>
            @endif

        </tab>

        @if($group->records->count() != 0)
            <tab name="Statistiken" icon="fa-chart-area">
                <template v-slot:default="props">

                    <h4 class="tw-my-4">Rekorde:</h4>
                    <div class="row justify-content-center">
                        <div class="col-sm-10 col-md-9 col-lg-7 col-xl-6">
                            <table class="table table-sm table-borderless text-left">
                                @forelse($group->records as $row)
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
                                @empty
                                    <h5>Diese Gruppe hat noch keine Rekorde.</h5>
                                @endforelse
                            </table>
                        </div>
                    </div>

                    <h4 class="tw-mt-8 tw-mb-4">Statistiken:</h4>
                    <div class="row justify-content-center">
                        <div class="col-sm-8 col-md-7 col-lg-5 col-xl-4">
                            <table class="table table-sm table-borderless text-left">
                                @forelse($group->stats as $row)
                                    @php $row = collect($row); @endphp
                                    <tr>
                                        <td{!! $row->contains('margin') ? ' class="pb-3"' : '' !!}>
                                            {!! $row->shift() !!}
                                        </td>
                                        <td>
                                            <b>{{ $row->shift() }}</b>
                                        </td>
                                    </tr>
                                @empty

                                @endforelse
                            </table>
                        </div>
                    </div>

                    @if($group->rounds->count() >= 5)
                        <h4 class="tw-mt-8">Anzahl der Spiele:</h4>
                        <group-graph :group_id="{{ $group->id }}" :key="props.tabKey"></group-graph>
                    @endif
                </template>
            </tab>
        @endif

        @if($badges->count() != 0)
            <tab name="Abzeichen" icon="fa-award">
                <div class="sm:tw-flex tw-max-w-2xl tw-mx-auto">
                    @forelse($badges as $type)
                        @php $typeDeutsch = $badges->keys()->get($loop->index) == 'points' ? 'Punkte' : 'Spiele'; @endphp
                        <div class="sm:tw-flex-1 tw-mb-10">
                            @foreach($type as $year)
                                <h5>Die meisten {{ $typeDeutsch }} {{ $type->keys()->get($loop->index) }}: </h5>
                                @foreach($year as $badge)
                                    <badge
                                            date="{{ $badge->date->formatLocalized('%B %Y') }}"
                                            name="{{ $badge->player->surname }}"
                                            value="{{ $badge->value }}"
                                            type="{{ $badge->type }}"
                                            path="{{ $badge->playerPath() }}"
                                    ></badge>
                                @endforeach
                            @endforeach
                        </div>
                    @empty
                        <h5 class="tw-mx-auto">Diese Runde hat noch keine Abzeichen.</h5>
                    @endforelse
                </div>
            </tab>
        @endif

        <tab name="Rundenarchiv" icon="fa-history">
            @include('rounds.inc.archiveTable')
        </tab>

        @if($group->creator->is(auth()->user()->player) || auth()->user()->id == 1)
            <tab name="Admin" icon="fa-cog">
                <div class="tw-max-w-sm tw-mx-auto">
                    @if(!$group->closed)
                        <p>Du bist der Ersteller dieser Liste und kannst sie <b>schließen</b>.</p>
                        <ul class="tw-list-outside tw-list-disc tw-pl-8 tw-text-left">
                            <li>Danach können <b>keine</b> neue Runden hinzugefügt werden</li>
                            <li>Bestehende Runden können <b>nicht</b> mehr verändert werden</li>
                        </ul>
                        <p>
                            Anschließend kann die Liste wieder geöffnet werden.
                        </p>

                        <a href="{{ route('groups.close', ['group' => $group->id, 'close' => 1]) }}"
                           class="btn btn-primary">
                            Liste schließen
                        </a>
                    @else
                        <p>Du bist der Ersteller dieser geschlossenen Liste und kannst sie wieder <b>öffnen</b>.</p>
                        <ul class="tw-list-outside tw-list-disc tw-pl-8 tw-text-left">
                            <li>Danach können wieder neue Runden hinzugefügt werden</li>
                            <li>Bestehende Runden können wieder verändert werden</li>
                        </ul>

                        <a href="{{ route('groups.close', ['group' => $group->id, 'close' => 0]) }}"
                           class="btn btn-primary">
                            Liste öffnen
                        </a>
                    @endif
                </div>
            </tab>
        @endif

    </tabs>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.myDataTable').DataTable({
                stateSave: true,
                dom: '<"tw-flex tw-justify-center"{{ $group->players->count() > 30 ? 'f' : '' }}>t<"my-3 tw-flex tw-justify-center"p><"my-3"l>',
                info: false,
                searching: true,
                columns: [
                    {"orderSequence": []},
                    {"orderSequence": ["asc", "desc"]},
                    {"orderSequence": ["desc", "asc"]},
                    {"orderSequence": ["desc", "asc"]},
                    {"orderSequence": ["desc", "asc"]},
                    {"orderSequence": ["desc", "asc"]},
                ],
                paging: {{ $group->players->count() > 15 ? "true" : "false" }},
                pageLength: -1,
                lengthMenu: [[15, 30, -1], [15, 30, "Alle"]],
                order: [2, "desc"],
                language: {
                    lengthMenu: "Zeige _MENU_ pro Seite",
                    paginate: {
                        next: "&rsaquo;",
                        previous: "&lsaquo;"
                    },
                    search: "Suche:"
                },
                drawCallback: function (settings) {
                    $('.dataTables_paginate > .pagination').addClass('pagination-sm');
                }
            });
            $('.myDataTable').removeClass("d-none");
        });
    </script>
@endpush
