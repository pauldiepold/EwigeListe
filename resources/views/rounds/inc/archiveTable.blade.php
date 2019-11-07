@if($rounds_count > 0)
    <div class="row justify-content-center my-4 d-none" id="archiveTable">
        <div class="col col-xl-9 col-lg-10">
            <table class="table roundsTable">
                <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Spiele</th>
                        <th>Teilnehmende Spieler</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <p class="mt-4">
                {{ $rounds_count }} Runden
            </p>
        </div>
    </div>

    @push('scriptsHead')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    @endpush

    @push('scripts')
        <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
        <script>
            $(document).ready(function () {
                $('.roundsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '/rounds/ajax/{{ $selectedGroup->id }}/{{ isset($player) ? $player->id : '' }}',
                    columns: [
                        {data: 'date', name: 'date', orderSequence: ["desc", "asc"]},
                        {data: 'games_count', name: 'games_count', orderSequence: ["desc", "asc"]},
                        {data: 'players', name: 'players', orderable: false},
                    ],
                    stateSave: false,
                    dom: 't<"my-3"p><"my-3"l>',
                    info: false,
                    searching: false,
                    paging: {{ $rounds_count > 15 ? "true" : "false" }},
                    pageLength: 15,
                    lengthMenu: [[15, 30, -1], [15, 30, "Alle"]],
                    order: [0, "desc"],
                    language: {
                        lengthMenu: "Zeige _MENU_ pro Seite",
                        paginate: {
                            next: "&rsaquo;",
                            previous: "&lsaquo;"
                        }
                    },
                    drawCallback: function (settings) {
                        $('.dataTables_paginate > .pagination').addClass('pagination-sm');
                    }
                });
                $('#archiveTable').removeClass("d-none");
            });
        </script>
    @endpush

@else
    <h5>Bisher wurden keine Runden gespielt.</h5>
@endif
