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
            <div class="row justify-content-center my-4">
                <div class="col col-xl-7 col-lg-8 col-md-9">
                    <table class="table nowrap myDataTable d-none table-responsive-sm">
                        <thead>
                            <tr class="border-bottom-thick" style="line-height:120%;">
                                <th>Name</th>
                                <th>Spiele</th>
                                <th>Punkte</th>
                                <th>Schnitt</th>
                                <th>Soli</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($group->players as $player)
                                @php $profile = $player->profiles->where('group_id', $group->id)->first(); @endphp

                                <tr class="{{ $player->id == auth()->user()->player->id ? ' bg-primary-light' : ''}}">

                                    <td style="max-width: 8rem; white-space: normal;">
                                        <a href="{{ $player->path() }}/{{ $group->id }}">
                                            {{ $player->surname }} {{ $player->name }}
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
                            @empty
                                Diese Gruppe hat noch keine Mitglieder.
                            @endforelse
                        </tbody>
                    </table>
                    <p class="mt-4">
                        {{ $group->players->count() }} Mitglieder
                    </p>
                </div>
            </div>

        </tab>

        <tab name="Statistiken" icon="fa-chart-area">
            <template v-slot:default="props">

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
                <group-graph :group_id="{{ $group->id }}" :key="props.tabKey"></group-graph>
            </template>
        </tab>

        <tab name="Abzeichen" icon="fa-award">
            <div class="tw-flex tw-flex-wrap tw-justify-center">
                @foreach($group->badges as $badge)
                    @isset($badge->player)
                        <badge
                            date="{{ $badge->date->formatLocalized('%B %Y') }}"
                            name="{{ $badge->player->surname }}"
                            value="{{ $badge->value }}"
                            type="{{ $badge->type }}"
                        ></badge>
                    @endisset
                @endforeach
            </div>
        </tab>

        <tab name="Rundenarchiv" icon="fa-history">
            @include('rounds.inc.archiveTable')
        </tab>

    </tabs>

    {{--<a class="btn btn-primary tw-my-6" href="/liste/calculate/{{ $group->id }}">Statistiken
        aktualisieren</a>
    <a class="btn btn-primary tw-my-6" href="/liste/calculateBadges/{{ $group->id }}">Badges
        aktualisieren</a>--}}

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.myDataTable').DataTable({
                stateSave: true,
                dom: 't<"my-3"p><"my-3"l>',
                info: false,
                searching: false,
                columns: [
                    {"orderSequence": ["asc", "desc"]},
                    {"orderSequence": ["desc", "asc"]},
                    {"orderSequence": ["desc", "asc"]},
                    {"orderSequence": ["desc", "asc"]},
                    {"orderSequence": ["desc", "asc"]},
                ],
                paging: {{ $group->players->count() > 15 ? "true" : "false" }},
                pageLength: -1,
                lengthMenu: [[15, 30, -1], [15, 30, "Alle"]],
                order: [1, "desc"],
                language: {
                    lengthMenu: "Zeige _MENU_ pro Seite",
                    paginate: {
                        next: "&rsaquo;",
                        previous: "&lsaquo;"
                    }
                },
                drawCallback: function (settings) {
                    $('.dataTables_paginate > .pagination').addClass('pagination-sm');
                    /*var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
                    pagination.toggle(this.api().page.info().pages > 1);*/
                }
            });
            $('.myDataTable').removeClass("d-none");
        });
    </script>
@endpush
