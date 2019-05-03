{{-- Input: paginated $rounds --}}
{{ $rounds->onEachSide(1)->links() }}

<div class="row justify-content-center my-4">
    <div class="col col-xl-8 col-lg-10">
        <table class="table">
            <tr>
                <th>Datum</th>
                <th>Anzahl Spiele</th>
                <th>Teilnehmende Spieler</th>
            </tr>
            @foreach ($rounds as $round)
                <tr{!! $round->players->pluck('id')->contains(Auth::user()->player->id) && $profile != Auth::user()->player->id ? ' class="bg-primary-light"' : '' !!}>
                    <td>
                        {{ date("d.m.Y - H:i", strtotime($round->created_at)) }}
                    </td>
                    <td>
                        {{ $round->games->count() }}
                    </td>
                    <td>
                        <a href="/rounds/{{ $round->id }}">
                            {{ nice_count($round->players->pluck('surname')->toArray()) }}
                        </a>
                    </td>
                </tr>
            @endforeach

        </table>
    </div>
</div>