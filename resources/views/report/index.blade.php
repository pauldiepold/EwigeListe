@extends('layouts.main')

@section('title', 'Reporting')

@section('heading', 'Reporting')

@section('content')

    <a class="btn btn-primary tw-my-6 tw-block" href="/listen/calculate">Alle Gruppen aktualisieren</a><br>
    <a class="btn btn-primary tw-my-6 tw-block" href="/players/calculate">Alle Spieler-Profile aktualisieren</a>
    <br>
    /liste/calculate/{group}<br>
    /liste/calculateBadges/{group}<br>
    /players/calculate/{player}<br><br>

    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-8 col-lg-7 col-xl-6">
            <table class="table table-sm" id="reportTable">
                <tr>
                    <th>Name</th>
                    <th>Act</th>
                    <th>von</th>
                    <th>bis</th>
                    <th>am</th>
                </tr>

                @foreach($reportData as $visit)
                    @if(strcmp($visit->get(0), 'Anonym'))
                        <tr>
                            <td>
                                <a href="" data-toggle="collapse" data-target="#collapseRow{{ $loop->index }}">
                                    {{ $visit->get(0) }}
                                </a>
                            </td>
                            <td>{{ $visit->get(4) }}</td>
                            <td>{{ substr($visit->get(2), 0, -3) }}</td>
                            <td>{{ substr($visit->get(3), 0, -3) }}</td>
                            <td>{{ $visit->get(1) }}</td>
                        </tr>
                        <tr class="collapse" id="collapseRow{{ $loop->index }}" data-parent="#reportTable">
                            <td colspan="5">
                                <table class="table-borderless">
                                    @foreach($visit->get(5) as $action)
                                        <tr>
                                            <td>{{ $action['title'] }}:</td>
                                            <td>
                                                <a href="{{ str_replace('www.', '', $action['url']) }}">{{ substr(str_replace('www.', '', $action['url']), 22) }}</a>
                                            </td>
                                            <td>{{ array_key_exists('timeSpent', $action) ? floor($action['timeSpent']/60) . "m " . $action['timeSpent']%60 . "s": '' }}</td>
                                        <!-- <td>{{ Carbon\Carbon::createFromTimestamp($action['timestamp'], 'Europe/Amsterdam')->formatLocalized('%H:%M') }}</td> -->
                                            <td>{{ substr($action['serverTimePretty'], 10) }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </table>
        </div>
    </div>

@endsection
