<div class="text-muted" style="font-size:0.88rem;">
    {{--
@if($round->games->count() >= 4)
    @php
        $gameLengthSeconds = (strtotime($lastGame->created_at) - strtotime($round->games->first()->created_at)) / ($round->games->count() - 1);
        $gameLength = floor($gameLengthSeconds / 60) . ' Minuten und ' . ($gameLengthSeconds % 60) . ' Sekunden';
    @endphp

    @if( ($gameLengthSeconds / 60 >= 4) && ($gameLengthSeconds / 60 <= 25))
    <p class="mb-0 mt-2">
        Durchschnittliche Spieldauer: {{ $gameLength }}
    </p>
    @endif
@endif
--}}
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
    @if($lastGame)
        <p class="mb-0 mt-4">
            Anzahl Spiele: {{ $round->games->count() }}
        </p>
    @endif
</div>
