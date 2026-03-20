@extends('layouts.main')

@section('title', 'Reporting')

@section('heading', 'Reporting')

@section('content')

    <a class="btn btn-primary my-6 block" href="/listen/calculate">Alle Gruppen aktualisieren</a><br>
    <a class="btn btn-primary my-6 block" href="/players/calculate">Alle User-Profile aktualisieren</a>
    <br>
    /liste/calculate/{group}<br>
    /liste/calculateBadges/{group}<br>
    /players/calculate/{player}<br><br>

    <div class="flex justify-center">
        <div class="w-full sm:w-10/12 md:w-8/12 lg:w-7/12 xl:w-6/12">
            <table class="table table-sm" id="reportTable">
                <tr>
                    <th>Name</th>
                    <th>Act</th>
                    <th>von</th>
                    <th>bis</th>
                    <th>am</th>
                </tr>
                @if(isset($reportData))
                @foreach($reportData as $visit)
                    @if(strcmp($visit->get(0), 'Anonym'))
                        <tr>
                            <td>
                                <a href="#"
                                   @click.prevent="$dispatch('toggle-report-row', { index: {{ $loop->index }} })"
                                   class="cursor-pointer underline text-blue-DEFAULT">
                                    {{ $visit->get(0) }}
                                </a>
                            </td>
                            <td>{{ $visit->get(4) }}</td>
                            <td>{{ substr($visit->get(2), 0, -3) }}</td>
                            <td>{{ substr($visit->get(3), 0, -3) }}</td>
                            <td>{{ $visit->get(1) }}</td>
                        </tr>
                        <tr x-data="{ visible: false }"
                            x-show="visible"
                            @toggle-report-row.window="if ($event.detail.index === {{ $loop->index }}) visible = !visible"
                            style="display: none;">
                            <td colspan="5">
                                <table class="table-borderless">
                                    @foreach($visit->get(5) as $action)
                                        <tr>
                                            <td>{{ $action['title'] }}:</td>
                                            <td>
                                                <a href="{{ str_replace('www.', '', $action['url']) }}">{{ substr(str_replace('www.', '', $action['url']), 22) }}</a>
                                            </td>
                                            <td>{{ array_key_exists('timeSpent', $action) ? floor($action['timeSpent']/60) . "m " . $action['timeSpent']%60 . "s": '' }}</td>
                                            <td>{{ substr($action['serverTimePretty'], 10) }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                    @endif
                @endforeach
                @endif
            </table>
        </div>
    </div>

@endsection
