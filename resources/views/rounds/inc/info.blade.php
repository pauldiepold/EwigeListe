@if($lastGame)
    <p>
        Anzahl Spiele: {{ $round->games->count() }}
    </p>
@endif
<p class="mb-1">
    Runde gestartet von {{ $round->createdBy->surname }}
    <br class="d-block d-sm-none"> {{ printDate($round->created_at) }}.
</p>
@if($lastGame)
    <p>
        Letztes Spiel eingetragen {{ printDate($lastGame->created_at) }}.
    </p>
@endif