@extends('layouts.main')

@section('title', 'Ewige Liste')

@section('heading', 'Ewige Liste')

@section('content')
    <table class="table mx-auto table-responsive-sm">
        <tr>
            <th>Name</th>
            <th>Spiele</th>
            <th>Punkte</th>
            <th>Schnitt</th>
            <th>Soli</th>
        </tr>

        @foreach ($players as $player)
            @php $profile = $player->profile; @endphp
            @if (!$player->hide)
            <tr>
                <td>
                    <a href="/players/{{ $player->id }}">
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
            @endif
        @endforeach
    </table>

@endsection