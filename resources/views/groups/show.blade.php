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
                                        <a href="{{ $player->path() }}">
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
        </tab>

        <tab name="Rundenarchiv" icon="fa-history">
            @include('rounds.inc.archiveTable')
        </tab>

    </tabs>
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
                pageLength: 15,
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
