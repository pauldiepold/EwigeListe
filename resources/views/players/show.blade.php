@extends('layouts.main')

@section('title', 'Userprofil')

@section('heading')
    Profil von <br class="d-block d-sm-none">{{ $player->surname }} {{ $player->name }}
@endsection

@section('content')

    @if(substr($player->user->avatar_path, -11) != 'default.jpg')
        <img src="{{ $player->user->avatar_path }}"
             class="tw-mx-auto tw-mb-4 tw-h-32 tw-w-32 tw-rounded-full">
    @endif

    @can('update', $player->user)
        <a href="{{ route('users.edit', [$player->user]) }}" class="btn btn-outline-primary tw-mb-3">
            <i class="fas fa-user-cog tw-mr-2"></i>Konto-Einstellungen
        </a>
    @endcan

    <tabs>
        <tab name="Statistiken" icon="fa-user" :selected="true">

            <select-liste>
                @foreach($player->groups as $group)
                    <option value="{{ $player->path() . '/' . $group->id . '#statistiken'}}"
                            {{ $selectedGroup->id == $group->id ? ' selected' : '' }}>
                        {{ $group->name }}
                    </option>
                @endforeach
            </select-liste>

            @if(isset($profile->games) && $profile->games >= 10)
                <div class="row justify-content-center">
                    <div class="col-sm-8 col-md-6 col-lg-5 col-xl-4">
                        <table class="table table-sm table-borderless text-left">
                            @if($profile->badges()->count() > 0)
                                <tr>
                                    <td class="pb-4">Monatsrekorde:</td>
                                    <td class="font-weight-bold pb-4">{{ $profile->badges()->count() }}
                                        <i class="fas fa-crown tw-ml-1 tw-text-yellow-500 tw-text-xl"></i>
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td>Aktuelle Punktzahl:</td>
                                <td class="font-weight-bold">{{ $profile->points }}</td>
                            </tr>
                            @if($profile->pointsThisMonth != 0)
                                <tr>
                                    <td>Punkte in diesem Monat:</td>
                                    <td class="font-weight-bold">{{ $profile->pointsThisMonth }}</td>
                                </tr>
                            @endif
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
                                <td class="font-weight-bold pb-4">{{ $profile->pointsPerLose }}</td>
                            </tr>
                            <tr>
                                <td>Spiele:</td>
                                <td class="font-weight-bold">{{ $profile->games }}</td>
                            </tr>
                            @if ($profile->gamesThisMonth > 0)
                                <tr>
                                    <td>Spiele in diesem Monat:</td>
                                    <td class="font-weight-bold">{{ $profile->gamesThisMonth }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td>Spiele pro Tag:</td>
                                <td class="font-weight-bold">{{ $profile->gamesPerDay }}</td>
                            </tr>
                            <tr>
                                <td>Gewonnen / Verloren:</td>
                                <td class="font-weight-bold">{{ $profile->gamesWon }} / {{ $profile->gamesLost }}</td>
                            </tr>
                            <tr>
                                <td class="pb-4">Gewinnrate:</td>
                                <td class="font-weight-bold pb-4">{{ $profile->winrate }}%</td>
                            </tr>
                            <tr>
                                <td {!! $profile->normalGames == 0 ? 'class="pb-4"' : '' !!}>Normalspiele:</td>
                                <td class="font-weight-bold">{{ $profile->normalGames }}</td>
                            </tr>
                            @if($profile->normalGames > 0)
                                <tr>
                                    <td>Gewonnen / Verloren:</td>
                                    <td class="font-weight-bold">{{ $profile->normalGamesWon }} / {{ $profile->normalGamesLost }}</td>
                                </tr>
                                <tr>
                                    <td class="pb-4">Normalspiel-Gewinnrate:</td>
                                    <td class="font-weight-bold pb-4">{{ $profile->normalGameWinrate }}%</td>
                                </tr>
                            @endif
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
                                    <td>Punkte durch Soli:</td>
                                    <td class="font-weight-bold">{{ $profile->soloPoints }}</td>
                                </tr>
                                <tr>
                                    <td class="pb-4">Punkte pro Solo:</td>
                                    <td class="font-weight-bold pb-4">{{ $profile->pointsPerSolo }}</td>
                                </tr>
                            @endif

                            <tr>
                                <td class="pb-2">Meiste Spiele an einem Tag:
                                    <span class="tw-text-xs">
                                        <br>(am {{ date('j.n.Y', strtotime($profile->mostGamesDayDate)) }})
                                    </span>
                                </td>
                                <td class="font-weight-bold">{{ $profile->mostGamesDay }}</td>
                            </tr>

                            <tr>
                                <td class="pb-4">Meiste Spiele in einem Monat:
                                    <span class="tw-text-xs">
                                        <br>(im {{ $profile->mostGamesMonthDate->isoFormat('MMMM YYYY') }})
                                    </span>
                                </td>
                                <td class="font-weight-bold pb-4">{{ $profile->mostGamesMonth }}</td>
                            </tr>

                            <tr>
                                <td class="pb-2">Höchste Punktzahl:
                                    @if($profile->highestPoints != 0)
                                        <span class="tw-text-xs">
                                            <br>(am {{ date('j.n.Y', strtotime($profile->highestPointsDate)) }})
                                        </span>
                                    @endif
                                </td>
                                <td class="font-weight-bold">{{ $profile->highestPoints }}</td>
                            </tr>

                            <tr>
                                <td class="pb-4">Niedrigste Punktzahl:
                                    @if($profile->lowestPoints != 0)
                                        <span class="tw-text-xs">
                                            <br>(am {{ date('j.n.Y', strtotime($profile->lowestPointsDate)) }})
                                        </span>
                                    @endif
                                </td>
                                <td class="font-weight-bold pb-4">{{ $profile->lowestPoints }}</td>
                            </tr>

                            <tr>
                                <td class="pb-2">Längste Sieges-Strähne:
                                    @if( strcmp(date('j.n.Y', strtotime($profile->winStreakStart)), date('j.n.Y', strtotime($profile->winStreakEnd))) )
                                        <span class="tw-text-xs">
                                            <br>({{ date('j.n', strtotime($profile->winStreakStart)) }}
                                            - {{ date('j.n.Y', strtotime($profile->winStreakEnd)) }})
                                        </span>
                                    @else
                                        <span class="tw-text-xs">
                                            <br>(am {{ date('j.n.Y', strtotime($profile->winStreakStart)) }})
                                        </span>
                                    @endif
                                </td>
                                <td class="font-weight-bold">{{ $profile->winStreak }}</td>
                            </tr>

                            <tr>
                                <td class="pb-4">Längste Pech-Strähne:
                                    @if( strcmp(date('j.n.Y', strtotime($profile->loseStreakStart)), date('j.n.Y', strtotime($profile->loseStreakEnd))) )
                                        <span class="tw-text-xs">
                                            <br>({{ date('j.n', strtotime($profile->loseStreakStart)) }}
                                            - {{ date('j.n.Y', strtotime($profile->loseStreakEnd)) }})
                                        </span>
                                    @else
                                        <span class="tw-text-xs">
                                            <br>(am {{ date('j.n.Y', strtotime($profile->loseStreakStart)) }})
                                        </span>
                                @endif
                                <td class="font-weight-bold pb-4">{{ $profile->loseStreak }}</td>
                            </tr>
                            
                            <tr>
                                <td class="pb-2">Eingetragene Spiele:</td>
                                <td class="font-weight-bold">{{ $profile->gamesCreated }}</td>
                            </tr>
                            <tr>
                                <td class="">Anteil eingetragener Spiele:
                                    <span class="tw-text-xs">
                                        <br>(eingetragene Spiele inklusive Mehrspielerrunden pro Anzahl eigener Spiele)
                                    </span>
                                </td>
                                <td class="font-weight-bold">{{ $profile->gamesCreateRate }}%</td>
                            </tr>
                        </table>
                        @if($profile->games > 150)
                            <hr>
                            <p class="tw-font-bold">Opportunitätskosten:</p>
                            <table class="table table-sm table-borderless text-left">
                                <tr>
                                    <td>Zeit:</td>
                                    <td>
                                        @if($profile->games * 8 / 60 / 24 > 1)
                                            {{ floor($profile->games * 8 / 60 / 24 ) }}d
                                        @endif
                                        {{ floor(($profile->games * 8 / 60) % 24) }}h
                                        {{ ($profile->games * 8) % 60 }}min
                                    </td>
                                </tr>
                                <tr>
                                    <td>Entgangenes Hiwi-Gehalt:</td>
                                    <td>{{ number_format($profile->games * 8 / 60 * 11.5, 2, ',', '.') }}€</td>
                                </tr>
                                <tr>
                                    <td>ECTS:</td>
                                    <td>{{ number_format($profile->games * 8 / 60 / 30, 1, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Vorlesungsdoppelstunden:</td>
                                    <td>{{ number_format($profile->games * 8 / 90, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        @endif
                    </div>
                </div>


            @else
                <h5>Statistiken werden ab dem 10. Spiel angezeigt.</h5>
            @endif
            <p class="tw-mt-6">
                @if($profile->group_id == 1)
                    Registriert seit dem
                @else
                    Der Liste beigetreten am
                @endif
                {{ $profile->created_at->format('d.m.Y') }}</p>
        </tab>
        <tab name="Graphen" icon="fa-chart-area">
            <template v-slot:default="props">

                <select-liste>
                    @foreach($player->groups as $group)
                        <option value="{{ $player->path() . '/' . $group->id . '#graphen'}}"
                                {{ $selectedGroup->id == $group->id ? ' selected' : '' }}>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select-liste>

                @if(isset($profile->games) && $profile->games >= 10)
                    <profile-graphs :profile_id="{{ $profile->id }}" :key="props.tabKey"></profile-graphs>
                @else
                    <h5>Statistiken werden ab dem 10. Spiel angezeigt.</h5>
                @endif
            </template>
        </tab>

        <tab name="Abzeichen" icon="fa-award">

            <select-liste>
                @foreach($player->groups as $group)
                    <option value="{{ $player->path() . '/' . $group->id . '#abzeichen'}}"
                            {{ $selectedGroup->id == $group->id ? ' selected' : '' }}>
                        {{ $group->name }}
                    </option>
                @endforeach
            </select-liste>

            <div class="sm:tw-flex tw-max-w-2xl tw-mx-auto">
                @forelse($badges as $type)
                    <div class="sm:tw-flex-1 tw-mb-10">
                        @foreach($type as $badge)
                            @isset($badge->player)
                                <badge
                                        date="{{ $badge->date->isoFormat('MMMM YYYY') }}"
                                        name="{{ $badge->player->surname }}"
                                        value="{{ $badge->value }}"
                                        type="{{ $badge->type }}"
                                ></badge>
                            @endisset
                        @endforeach
                    </div>
                @empty
                    <h5 class="tw-mx-auto">Bisher keine Abzeichen auf dieser Liste.</h5>
                @endforelse
            </div>
        </tab>

        <tab name="Rundenarchiv" icon="fa-history">

            <select-liste>
                @foreach($player->groups as $group)
                    <option value="{{ $player->path() . '/' . $group->id . '#rundenarchiv'}}"
                            {{ $selectedGroup->id == $group->id ? ' selected' : '' }}>
                        {{ $group->name }}
                    </option>
                @endforeach
            </select-liste>

            @include('rounds.inc.archiveTable')

        </tab>

        <tab name="Listen" icon="fa-list-alt">
            <h4>Listen:</h4>
            @forelse($player->groups as $group)
                <div class="group tw-max-w-sm"><a href="{{ $group->path() }}">{{ $group->name }}</a></div>

            @empty
                <h5>Der Spiele ist bisher in keiner Liste.</h5>
            @endforelse
        </tab>
    </tabs>

@endsection
