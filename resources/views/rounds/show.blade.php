@extends('layout')

@section('title', 'Aktuelle Runde')

@section('heading', 'Aktuelle Runde')

@section('content')

    @can('update', $round)
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal">
            Spiel eintragen
        </button>

        <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title mx-auto" id="createModalLabel">Welche Spieler haben gewonnen?</h5>
                    </div>
                    <div class="modal-body">

                        <form method="POST" action="/rounds/{{ $round->id }}/game">
                            @csrf

                            @foreach ($activePlayers as $player)
                                <div class="custom-control custom-checkbox my-1">
                                    <input class="custom-control-input" type="checkbox" value="{{ $player->id }}"
                                           id="player{{ $player->id }}" name="winners[]">
                                    <label class="custom-control-label font-weight-bold" for="player{{ $player->id }}">
                                        {{ $player->surname }} {{ $player->name }}
                                    </label>
                                </div>
                            @endforeach

                            <div class="form-row my-4 mx-auto justify-content-center">
                                <div class="col-xs-6 col-xs-offset-3">
                                    <input class="form-control" type="number" min="-4" max="16" value="0" name="points">
                                    <label for="points" class="control-label font-weight-bold">Punkte</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Bestätigen</button>
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary mx-auto" data-dismiss="modal">Schließen</button>
                    </div>
                </div>
            </div>
        </div>

    @endcan

    @include('include.showRound')

    @if ($round->games->count() != 0)
        @can('update', $round)
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#deleteModal">
                Letztes Spiel löschen
            </button>

            @php $lastGame = $round->getLastGame() @endphp

            <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title mx-auto" id="deleteModalLabel">Willst du dieses Spiel wirklich
                                löschen?</h5>
                        </div>
                        <div class="modal-body">
                            @foreach ($lastGame->players as $player)
                                <p>{{ $player->surname }} {{ $player->name }}
                                    : {{ $lastGame->players()->where('player_id', $player->id)->first()->pivot->points }}</p>
                            @endforeach

                            <form method="POST" action="/games/{{ $lastGame->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-primary mt-3">Spiel Löschen</button>
                            </form>
                            @include('error')
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary mx-auto" data-dismiss="modal">Schließen
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            @if (count($errors) > 0)
                <script type="text/javascript">
                    $(document).ready(function () {
                        $('#deleteModal').modal({show: true});
                    });
                </script>
            @endif
        @endcan
    @endif

@endsection