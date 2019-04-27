<p>
	Anzahl Spiele: {{ $round->games->count() }}<br>
	Runde gestartet von {{ $round->createdBy->surname }} {{ printDate($round->created_at) }}.<br>
	<br>
	Letztes Spiel eingetragen {{ printDate($lastGame->created_at) }}.
</p>