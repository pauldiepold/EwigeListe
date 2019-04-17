@extends('layouts.main')

@section('title', 'Spielerprofil')

@section('heading')
    Spielerprofil von {{ $player->surname }} {{ $player->name }}
@endsection

@section('content')
    @include('include.back')

    <div class="row justify-content-center">
        <div class="col-sm-8 col-md-6 col-lg-5 col-xl-4">
            <table class="table table-sm table-borderless text-left">
                <tr>
                    <td>Aktuelle Punktzahl:</td>
                    <td class="font-weight-bold">{{ $profile->points }}</td>
                </tr>
                <tr>
                    <td>Punkte pro Spiel:</td>
                    <td class="font-weight-bold">{{ $profile->pointsPerGame }}</td>
                </tr>
                <tr>
                    <td>Punkte pro gewonnenem Spiel:</td>
                    <td class="font-weight-bold">{{ $profile->pointsPerWin }}</td>
                </tr>
                <tr>
                    <td class="pb-4">Punkte pro verlorenem Spiel:</td>
                    <td class="font-weight-bold">{{ $profile->pointsPerLose }}</td>
                </tr>
                <tr>
                    <td>Spiele:</td>
                    <td class="font-weight-bold">{{ $profile->games }}</td>
                </tr>
                <tr>
                    <td>Gewonnen / Verloren:</td>
                    <td class="font-weight-bold">{{ $profile->gamesWon }} / {{ $profile->gamesLost }}</td>
                </tr>
                <tr>
                    <td class="pb-4">Gewinnrate:</td>
                    <td class="font-weight-bold">{{ $profile->winrate }}%</td>
                </tr>
                <tr>
                    <td>Soli:</td>
                    <td class="font-weight-bold">{{ $profile->soli }}</td>
                </tr>
                <tr>
                    <td>Gewonnen / Verloren:</td>
                    <td class="font-weight-bold">{{ $profile->soliWon }} / {{ $profile->soliLost }}</td>
                </tr>
                <tr>
                    <td>Solo-Gewinnrate:</td>
                    <td class="font-weight-bold">{{ $profile->soloWinrate }}%</td>
                </tr>
                <tr>
                    <td class="pb-4">Punkte durch Soli:</td>
                    <td class="font-weight-bold">{{ $profile->soloPoints }}</td>
                </tr>

                <tr>
                    <td>Meiste Spiele an einem Tag:<br>
                        {{ date('j.n.Y', strtotime($profile->mostGamesDayDate)) }}</td>
                    <td class="font-weight-bold">{{ $profile->mostGamesDay }}</td>
                </tr>

                <tr>
                    <td>Meiste Spiele in einem Monat:<br>
                        {{ date('F y', strtotime($profile->mostGamesMonthDate)) }}</td>
                    <td class="font-weight-bold">{{ $profile->mostGamesMonth }}</td>
                </tr>

                <tr>
                    <td>Höchste Punktzahl:<br>
                        {{ $profile->highestPointsDate }}</td>
                    <td class="font-weight-bold">{{ $profile->highestPoints }}</td>
                </tr>

                <tr>
                    <td>Niedrigste Punktzahl:<br>
                        {{ $profile->lowestPointsDate }}</td>
                    <td class="font-weight-bold">{{ $profile->lowestPoints }}</td>
                </tr>

                <tr>
                    <td>Längste Sieges-Strähne:<br>
                        Start: {{ $profile->winStreakStart }}<br>
                        Ende: {{ $profile->winStreakEnd }}</td>
                    <td class="font-weight-bold">{{ $profile->winStreak }}</td>
                </tr>

                <tr>
                    <td>Längste Pech-Strähne:<br>
                        Start: {{ $profile->loseStreakStart }}<br>
                        Ende: {{ $profile->loseStreakEnd }}</td>
                    <td class="font-weight-bold">{{ $profile->loseStreak }}</td>
                </tr>
            </table>
        </div>
    </div>

    <hr>
    <h4>Zuletzt gespielte Runden:</h4>
    @include('rounds.inc.archiveTable')

@endsection