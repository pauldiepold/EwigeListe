@extends('layout')

@section('title', 'Aktuelle Runde')

@section('heading', 'Aktuelle Runde')

@section('content')

    <a href="/games/create" class="btn btn-primary">Spiel eintragen</a>

    <div class="row justify-content-center my-4">
        <div class="col col-xl-6 col-lg-8 col-md-10 col-sm-12">
            <div class="table-responsive">
                <table class="table table-borderless">
                    <tr class="border-bottom-thick">
                        @php  $punkte_addiert = array(); @endphp
                        @foreach ($round->players as $player)
                            @php  $punkte_addiert[] = 0; @endphp
                            <th{!!  ( $player->pivot->index == $round->getDealerIndex() ) ? ' class="text-success"' : '' !!}>
                                {{ $player->surname }}
                            </th>

                        @endforeach
                        <th>
                            Punkte
                        </th>
                    </tr>

                    @foreach ($games as $game)
                        @php $i = 0; @endphp
                        <tr class="{!! ( ($game->getDealerIndex() + 1  == $game->round->players->count() ) && $game->solo == 0 ) ? 'border-bottom-thick' : ''!!}{!! (  $game->solo == 1 ) ? ' bg-light' : ''!!}">
                            @foreach ($round->players as $player)
                                @if ($game->players->contains($player))
                                    <td{!! ($game->players()->where('player_id', $player->id)->first()->pivot->won == 1 ? ' class="font-weight-bold"' : '') !!}>
                                        @php
                                            $punkte_addiert[$i] = $punkte_addiert[$i] +  $game->players()->where('player_id', $player->id)->first()->pivot->points;
                                        @endphp
                                        {{ $punkte_addiert[$i] }}
                                    </td>
                                @else
                                    <td>-</td>
                                @endif

                                @php $i++; @endphp
                            @endforeach
                            <td>
                                {{ $game->points }}
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>

    <a href="/games/delete/{{ $round->id }}" class="btn btn-primary">Letztes Spiel löschen</a>


@endsection