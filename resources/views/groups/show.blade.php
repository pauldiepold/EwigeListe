@extends('layouts.main')

@section('title', 'Gruppe - ' . $group->name)

@section('heading', 'Gruppe - ' . $group->name)

@section('content')
    <h4>Mitglieder:</h4>

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

                    <tr class="{{ $player->id == auth()->user()->player->id ? ' bg-primary-light' : ''}}"
                        style="{{ $player->payment == 1 ? 'background-color: #eeffe6;' : ''}}">

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

    <h4 class="tw-mt-10" id="lastRounds">In dieser Gruppe gespielte Runden:</h4>

    @include('rounds.inc.archiveTable')

@endsection

@push('scriptsHead')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/3.2.6/css/fixedColumns.bootstrap4.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
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
                lengthMenu: [[ 15, 30, -1], [ 15, 30, "Alle"]],
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
                    var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
                    pagination.toggle(this.api().page.info().pages > 1);
                }
            });
            $('.myDataTable').removeClass("d-none");
        });
    </script>
@endpush
