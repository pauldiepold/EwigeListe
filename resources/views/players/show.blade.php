@extends('layouts.main')

@section('title', 'Spielerprofil')

@section('heading')
    Profil von <br class="d-block d-sm-none">{{ $player->surname }} {{ $player->name }}
@endsection

@section('content')

    @can('update', $player)
        <a href="{{ route('players.edit', [$player]) }}" class="btn btn-outline-primary tw-mb-3">
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
                            @if($profile->pointsThisMonth > 0)
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
                                    <span class="small-text">
                                        <br>(am {{ date('j.n.Y', strtotime($profile->mostGamesDayDate)) }})
                                    </span>
                                </td>
                                <td class="font-weight-bold">{{ $profile->mostGamesDay }}</td>
                            </tr>

                            <tr>
                                <td class="pb-4">Meiste Spiele in einem Monat:
                                    <span class="small-text">
                                        <br>(im {{ $profile->mostGamesMonthDate->formatLocalized('%B %Y') }})
                                    </span>
                                </td>
                                <td class="font-weight-bold pb-4">{{ $profile->mostGamesMonth }}</td>
                            </tr>

                            <tr>
                                <td class="pb-2">Höchste Punktzahl:
                                    @if($profile->highestPoints != 0)
                                        <span class="small-text">
                                            <br>(am {{ date('j.n.Y', strtotime($profile->highestPointsDate)) }})
                                        </span>
                                    @endif
                                </td>
                                <td class="font-weight-bold">{{ $profile->highestPoints }}</td>
                            </tr>

                            <tr>
                                <td class="pb-4">Niedrigste Punktzahl:
                                    @if($profile->lowestPoints != 0)
                                        <span class="small-text">
                                            <br>(am {{ date('j.n.Y', strtotime($profile->lowestPointsDate)) }})
                                        </span>
                                    @endif
                                </td>
                                <td class="font-weight-bold pb-4">{{ $profile->lowestPoints }}</td>
                            </tr>

                            <tr>
                                <td class="pb-2">Längste Sieges-Strähne:
                                    @if( strcmp(date('j.n.Y', strtotime($profile->winStreakStart)), date('j.n.Y', strtotime($profile->winStreakEnd))) )
                                        <span class="small-text">
                                            <br>({{ date('j.n', strtotime($profile->winStreakStart)) }}
                                            - {{ date('j.n.Y', strtotime($profile->winStreakEnd)) }})
                                        </span>
                                    @else
                                        <span class="small-text">
                                            <br>(am {{ date('j.n.Y', strtotime($profile->winStreakStart)) }})
                                        </span>
                                    @endif
                                </td>
                                <td class="font-weight-bold">{{ $profile->winStreak }}</td>
                            </tr>

                            <tr>
                                <td class="">Längste Pech-Strähne:
                                    @if( strcmp(date('j.n.Y', strtotime($profile->loseStreakStart)), date('j.n.Y', strtotime($profile->loseStreakEnd))) )
                                        <span class="small-text">
                                            <br>({{ date('j.n', strtotime($profile->loseStreakStart)) }}
                                            - {{ date('j.n.Y', strtotime($profile->loseStreakEnd)) }})
                                        </span>
                                    @else
                                        <span class="small-text">
                                            <br>(am {{ date('j.n.Y', strtotime($profile->loseStreakStart)) }})
                                        </span>
                                @endif
                                <td class="font-weight-bold">{{ $profile->loseStreak }}</td>
                            </tr>
                        </table>
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
                                    date="{{ $badge->date->formatLocalized('%B %Y') }}"
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
