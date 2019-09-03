@extends('layouts.main')

@section('title', 'Ewige Liste')

@section('heading', 'Ewige Liste')

@section('content')
<a href="/profiles" class="btn btn-primary">Ausführliche Statistiken</a>

    <div class="row justify-content-center my-4">
        <div class="col col-xl-7 col-lg-8 col-md-9">
            <table class="table">
                <tr class="border-bottom-thick" style="line-height:120%;">
                    <th>
                        @php //$orderBy == 'surname' ? ($order == 'up' ? 'up' : 'down') : 'down' @endphp
                        <a href="/players/surname/{{ $orderBy == 'surname' ? ($order == 'up' ? 'down' : 'up') : 'down'}}"
                           class="{{ $orderBy == 'surname' ? ''  : 'text-dark'}}">
                            Name
                            <br class="d-inline d-sm-none">
                            <i class="fas fa-sort{{ $orderBy == 'surname' ? '-' . $order : ' text-dark' }}"></i>
                        </a>
                    </th>
                    <th>
                        <a href="/players/games/{{ $orderBy == 'games' ? ($order == 'up' ? 'down' : 'up') : 'down' }}"
                           class="{{ $orderBy == 'games' ? ''  : 'text-dark'}}">
                            Spiele
                            <br class="d-inline d-sm-none">
                            <i class="fas fa-sort{{ $orderBy == 'games' ? '-' . $order : ' text-dark' }}"></i>
                        </a>
                    </th>
                    <th>
                        <a href="/players/points/{{ $orderBy == 'points' ? ($order == 'up' ? 'down' : 'up') : 'down' }}"
                           class="{{ $orderBy == 'points' ? ''  : 'text-dark'}}">
                            Punkte
                            <br class="d-inline d-sm-none">
                            <i class="fas fa-sort{{ $orderBy == 'points' ? '-' . $order : ' text-dark' }}"></i>
                        </a>
                    </th>
                    <th>
                        <a href="/players/pointsPerGame/{{ $orderBy == 'pointsPerGame' ? ($order == 'up' ? 'down' : 'up') : 'down' }}"
                           class="{{ $orderBy == 'pointsPerGame' ? ''  : 'text-dark'}}">
                            Schnitt
                            <br class="d-inline d-sm-none">
                            <i class="fas fa-sort{{ $orderBy == 'pointsPerGame' ? '-' . $order : ' text-dark' }}"></i>
                        </a>
                    </th>
                    <th>
                        <a href="/players/soli/{{ $orderBy == 'soli' ? ($order == 'up' ? 'down' : 'up') : 'down' }}"
                           class="{{ $orderBy == 'soli' ? ''  : 'text-dark'}}">
                            Soli
                            <br class="d-inline d-sm-none">
                            <i class="fas fa-sort{{ $orderBy == 'soli' ? '-' . $order : ' text-dark' }}"></i>
                        </a>
                    </th>
                </tr>

                @foreach ($players as $player)
                    @php $profile = $player->profile; @endphp
                    <tr class="{{ $player->id == Auth::user()->player->id ? ' bg-primary-light' : ''}}" style="{{ $player->payment == 1 ? 'background-color: #eeffe6;' : ''}}">
                        <td>
                            <a href="/profiles/{{ $player->id }}">
                                {{ $player->surname }} {{ $player->name }}
                            </a>
                        </td>
                        <td>
                            {{ $profile->games }}
                        </td>
                        <td>
                            {{ $profile->points }}
                        </td>
                        <td>
                            {{ $profile->pointsPerGame }}
                        </td>
                        <td>
                            {{ $profile->soli }}
                        </td>
                    </tr>
                @endforeach
            </table>
            <p class="mt-4">
                Anzahl registrierter Spieler: {{ $playersCount }}
            </p>
        </div>
    </div>
@endsection