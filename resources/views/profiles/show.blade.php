@extends('layouts.main')

@section('title', 'Spielerprofil')

@section('heading')
    Spielerprofil von <br class="d-block d-sm-none">{{ $player->surname }} {{ $player->name }}
@endsection

@section('content')
    @include('include.back')

    @if($profile->games >= 10)
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
                        <td>Spiele in diesem Monat:</td>
                        <td class="font-weight-bold">{{ $profile->gamesThisMonth }}</td>
                    </tr>
					@if ($profile->gamesPerDay != null)
                    <tr>
						<td>Spiele pro Tag:</td>
                        <td class="font-weight-bold">{{ $profile->gamesPerDay }}</td>
                    </tr>
					@endif
                    <tr>
                        <td>Gewonnen / Verloren:</td>
                        <td class="font-weight-bold">{{ $profile->gamesWon }} / {{ $profile->gamesLost }}</td>
                    </tr>
                    <tr>
                        <td class="pb-4">Gewinnrate:</td>
                        <td class="font-weight-bold">{{ $profile->winrate }}%</td>
                    </tr>
                    <tr>
                        <td {!! $profile->soli == 0 ? 'class="pb-4"' : '' !!}>Soli:</td>
                        <td class="font-weight-bold">{{ $profile->soli }}</td>
                    </tr>
                    @if($profile->soli > 0)
                        <tr>
                            <td>Gewonnen / Verloren:</td>
                            <td class="font-weight-bold">{{ $profile->soliWon }} / {{ $profile->soliLost }}</td>
                        </tr>
                        <tr>
                            <td>Spiele bis Solo:</td>
                            <td class="font-weight-bold">{{ $profile->soloRate }}</td>
                        </tr>
                        <tr>
                            <td>Solo-Gewinnrate:</td>
                            <td class="font-weight-bold">{{ $profile->soloWinrate }}%</td>
                        </tr>
                        <tr>
                            <td class="pb-4">Punkte durch Soli:</td>
                            <td class="font-weight-bold">{{ $profile->soloPoints }}</td>
                        </tr>
                    @endif

                    <tr>
                        <td class="pb-2">Meiste Spiele an einem Tag:
                            <span class="small-text"><br>(am {{ date('j.n.Y', strtotime($profile->mostGamesDayDate)) }})</span>
                        </td>
                        <td class="font-weight-bold">{{ $profile->mostGamesDay }}</td>
                    </tr>

                    <tr>
                        <td class="pb-4">Meiste Spiele in einem Monat:
                            <span class="small-text"><br>(im {{ $profile->mostGamesMonthDate->formatLocalized('%B %Y') }})</span>
                        </td>
                        <td class="font-weight-bold">{{ $profile->mostGamesMonth }}</td>
                    </tr>

                    <tr>
                        <td class="pb-2">Höchste Punktzahl:
                            @if($profile->highestPoints != 0)
                                <span class="small-text"><br>(am {{ date('j.n.Y', strtotime($profile->highestPointsDate)) }})</span>
                            @endif
                        </td>
                        <td class="font-weight-bold">{{ $profile->highestPoints }}</td>
                    </tr>

                    <tr>
                        <td class="pb-4">Niedrigste Punktzahl:
                            @if($profile->lowestPoints != 0)
                                <span class="small-text"><br>(am {{ date('j.n.Y', strtotime($profile->lowestPointsDate)) }})</span>
                            @endif
                        </td>
                        <td class="font-weight-bold">{{ $profile->lowestPoints }}</td>
                    </tr>

                    <tr>
                        <td class="pb-2">Längste Sieges-Strähne:
                            @if( strcmp(date('j.n.Y', strtotime($profile->winStreakStart)), date('j.n.Y', strtotime($profile->winStreakEnd))) )
                                <span class="small-text"><br>({{ date('j.n', strtotime($profile->winStreakStart)) }} - {{ date('j.n.Y', strtotime($profile->winStreakEnd)) }})</span>
                            @else
                                <span class="small-text"><br>(am {{ date('j.n.Y', strtotime($profile->winStreakStart)) }})</span>
                            @endif
                        </td>
                        <td class="font-weight-bold">{{ $profile->winStreak }}</td>
                    </tr>

                    <tr>
                        <td class="">Längste Pech-Strähne:
                            @if( strcmp(date('j.n.Y', strtotime($profile->loseStreakStart)), date('j.n.Y', strtotime($profile->loseStreakEnd))) )
                                <span class="small-text"><br>({{ date('j.n', strtotime($profile->loseStreakStart)) }} - {{ date('j.n.Y', strtotime($profile->loseStreakEnd)) }})</span>
                            @else
                                <span class="small-text"><br>(am {{ date('j.n.Y', strtotime($profile->loseStreakStart)) }})</span>
                        @endif
                        <td class="font-weight-bold">{{ $profile->loseStreak }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>

        <profile-graphs :player_id="{{ $profile->player_id }}"></profile-graphs>
    @else
        <h5 class="mt-2">Statistiken werden ab dem 10. Spiel angezeigt!</h5>
    @endif

    @if($profile->games > 0)
        <hr>

        <h4 id="lastRounds">Zuletzt gespielte Runden:</h4>

        @include('rounds.inc.archiveTable', ['profile' => $profile->player_id])

    <hr>

    @include('comments.index', ['comments' => $profile->comments()->latest()->paginate(8, ['*'], 'comments'), 'route' => 'profile'])

    @endif

    @if(!$rounds->onFirstPage())
        @push('scripts')
            <script>
                $(document).ready(function () {
                    $('html, body').scrollTop($('#lastRounds').offset().top - 50);
                });
            </script>
        @endpush
    @endif

@endsection