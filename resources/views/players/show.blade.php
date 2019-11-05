@extends('layouts.main')

@section('title', 'Spielerprofil')

@section('heading')
    Profil von <br class="d-block d-sm-none">{{ $player->surname }} {{ $player->name }}
@endsection

@section('content')
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
                                <td class="font-weight-bold pb-4">{{ $profile->pointsPerLose }}</td>
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
                                    <td class="pb-4">Punkte durch Soli:</td>
                                    <td class="font-weight-bold pb-4">{{ $profile->soloPoints }}</td>
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
                <h5 class="mt-2">Statistiken werden ab dem 10. Spiel angezeigt!</h5>
            @endif
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

                @if($profile->games >= 10)
                    <profile-graphs :profile_id="{{ $profile->id }}" :key="props.tabKey"></profile-graphs>
                @else
                    <h5 class="mt-2">Statistiken werden ab dem 10. Spiel angezeigt!</h5>
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

            <div class="tw-flex tw-flex-wrap tw-justify-center">
                @forelse($badges as $badge)
                    @isset($badge->player)
                        <badge
                            date="{{ $badge->date->formatLocalized('%B %Y') }}"
                            name="{{ $badge->player->surname }}"
                            value="{{ $badge->value }}"
                            type="{{ $badge->type }}"
                        ></badge>
                    @endisset
                @empty
                    Bisher keine Abzeichen in dieser Runde.
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

            @if($player->games->count() > 0)
                @include('rounds.inc.archiveTable')
            @endif

        </tab>

        <tab name="Listen" icon="fa-list-alt">
            <h4>Listen:</h4>
            @forelse($player->groups as $group)
                <div class="group tw-max-w-sm"><a href="{{ $group->path() }}">{{ $group->name }}</a></div>

            @empty
                Der Spiele ist bisher in keiner Liste.
            @endforelse
        </tab>
    </tabs>

    {{--
    <a class="btn btn-primary tw-my-6" href="/players/calculate/{{ $player->id }}">Statistiken
        aktualisieren</a>--}}

@endsection
<script>
    import SelectListe from "../../js/components/components/SelectListe";

    export default {
        components: {SelectListe}
    }
</script>
