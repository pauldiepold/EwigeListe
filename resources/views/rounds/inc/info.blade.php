<div class="text-muted tw-text-sm tw-mt-6" style="font-size:0.88rem;">
    @if ($round->games->count() != 0)
        <p class="mb-2">
            Anzahl Spiele: {{ $round->games->count() }}
        </p>
        @if($round->games->count() >= 4)
            @php
                $gameLengthSeconds = (strtotime($lastGame->created_at) - strtotime($round->games->first()->created_at)) / ($round->games->count() - 1);
                $gameLength = floor($gameLengthSeconds / 60) . ' Minuten und ' . ($gameLengthSeconds % 60) . ' Sekunden';
            @endphp

            @if( ($gameLengthSeconds / 60 >= 0) && ($gameLengthSeconds / 60 <= 25))
                <p>
                    Durchschnittliche Spieldauer: {{ $gameLength }}
                </p>
            @endif
        @endif
    @endif

    <p class="mb-2 mt-3">
        Runde gestartet von {{ $round->createdBy->surname }}
        <br class="d-block d-sm-none"> {{ printDate($round->created_at) }}.
    </p>
    @if($lastGame)
        <p>
            Letztes Spiel eingetragen @if($lastGame->createdBy)von {{ $lastGame->createdBy->surname }}@endif
            <br class="d-block d-sm-none"> {{ printDate($lastGame->created_at) }}.
        </p>
    @endif
</div>

