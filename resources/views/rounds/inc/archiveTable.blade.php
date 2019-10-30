{{-- Input: paginated $rounds --}}

@if(!$rounds->isEmpty())
    <div class="row justify-content-center my-4">
        <div class="col col-xl-9 col-lg-10">
            <table class="table roundsTable d-none">
                <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Spiele</th>
                        <th>Teilnehmende Spieler</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rounds as $round)
                        <tr class="{{ $round->players->pluck('id')->contains(auth()->user()->player->id) ? 'bg-primary-light' : '' }}">
                            <td data-sort="{{ $round->created_at }}">
                            {{ date("d.m.y", strtotime($round->created_at)) }} <!-- - H:i -->
                            </td>
                            <td>
                                {{ $round->games_count }}
                            </td>
                            <td>
                                <a href="{{ $round->path() }}">
                                    {{ nice_count($round->players->pluck('surname')->toArray()) }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p class="mt-4">
                {{ $rounds->count() }} Runden
            </p>
        </div>
    </div>
@else
    <p class="tw-mt-6 tw-font-bold">
        Es wurde keine Runde gefunden.
    </p>
@endif

@push('scriptsHead')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.roundsTable').DataTable({
                stateSave: false,
                dom: 't<"my-3"p><"my-3"l>',
                info: false,
                searching: false,
                columns: [
                    {orderSequence: ["asc", "desc"]},
                    {orderSequence: ["desc", "asc"]},
                    {orderable: false},
                ],
                paging: {{ $rounds->count() > 15 ? "true" : "false" }},
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
                    /*var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
                    pagination.toggle(this.api().page.info().pages > 1);*/
                }
            });
            $('.roundsTable').removeClass("d-none");
        });
    </script>
@endpush
