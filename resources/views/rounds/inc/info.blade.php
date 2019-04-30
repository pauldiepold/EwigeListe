<hr>
@if($lastGame)
    <p>
        Anzahl Spiele: {{ $round->games->count() }}
    </p>
@endif
<p class="mb-2">
    Runde gestartet von {{ $round->createdBy->surname }}
    <br class="d-block d-sm-none"> {{ printDate($round->created_at) }}.
</p>
@if($lastGame)
    <p>
        Letztes Spiel eingetragen @if($lastGame->createdBy)von {{ $lastGame->createdBy->surname }}@endif
        <br class="d-block d-sm-none"> {{ printDate($lastGame->created_at) }}.
    </p>
@endif