{{-- Input: paginated $rounds --}}
{{ $rounds->onEachSide(1)->links() }}

@if(!$rounds->isEmpty())
    <div class="row justify-content-center my-4">
        <div class="col col-xl-8 col-lg-10">
            <table class="table">
                <tr>
                    <th>Datum</th>
                    <th>Anzahl Spiele</th>
                    <th>Teilnehmende Spieler</th>
                </tr>
                @foreach ($rounds as $round)
                    <tr class="{{ $round->players->pluck('id')->contains(auth()->user()->player->id) ? 'bg-primary-light' : '' }}">
                        <td>
                            {{ date("d.m.Y - H:i", strtotime($round->created_at)) }}
                        </td>
                        <td>
                            {{ $round->games->count() }}
                        </td>
                        <td>
                            <a href="{{ $round->path() }}">
                                {{ nice_count($round->players->pluck('surname')->toArray()) }}
                            </a>
                        </td>
                    </tr>
                @endforeach

            </table>
        </div>
    </div>
@else
    <p class="tw-mt-6 tw-font-bold">
        Es wurde keine Runde gefunden.
    </p>
@endif
