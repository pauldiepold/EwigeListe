@extends('layouts.main')

@section('title', 'Ewige Liste')

@section('heading', 'Ewige Liste')

@section('content')
<style>
	a {
		color: black;
	}
</style>
<div class="row justify-content-center my-4">
    <div class="col col-xl-7 col-lg-8 col-md-9">
    <table class="table">
        <tr class="border-bottom-thick">
			<th>@php //$orderBy == 'surname' ? ($order == 'up' ? 'up' : 'down') : 'down' @endphp
            	<a href="/players/surname/{{ $orderBy == 'surname' ? ($order == 'up' ? 'down' : 'up') : 'down'}}" class="{{ $orderBy == 'surname' ? 'text-primary'  : ''}}">
					Name<br><i class="fas fa-sort-alpha-{{ $orderBy == 'surname' ? $order . ' text-primary' : 'down' }}"></i>						
				</a>
			</th>
            <th>
				<a href="/players/games/{{ $orderBy == 'games' ? ($order == 'up' ? 'down' : 'up') : 'down' }}" class="{{ $orderBy == 'games' ? 'text-primary'  : ''}}">
					Spiele<br><i class="fas fa-sort-amount-{{ $orderBy == 'games' ? $order . ' text-primary' : 'down' }}"></i>
				</a>
			</th>
            <th>
				<a href="/players/points/{{ $orderBy == 'points' ? ($order == 'up' ? 'down' : 'up') : 'down' }}" class="{{ $orderBy == 'points' ? 'text-primary'  : ''}}">
					Punkte<br><i class="fas fa-sort-amount-{{ $orderBy == 'points' ? $order . ' text-primary' : 'down' }}"></i>
				</a>
			</th>
            <th>
				<a href="/players/pointsPerGame/{{ $orderBy == 'pointsPerGame' ? ($order == 'up' ? 'down' : 'up') : 'down' }}" class="{{ $orderBy == 'pointsPerGame' ? 'text-primary'  : ''}}">
					Schnitt<br><i class="fas fa-sort-amount-{{ $orderBy == 'pointsPerGame' ? $order . ' text-primary' : 'down' }}"></i>
				</a>
			</th>
            <th>
				<a href="/players/soli/{{ $orderBy == 'soli' ? ($order == 'up' ? 'down' : 'up') : 'down' }}" class="{{ $orderBy == 'soli' ? 'text-primary'  : ''}}">
					Soli<br><i class="fas fa-sort-amount-{{ $orderBy == 'soli' ? $order . ' text-primary' : 'down' }}"></i>
				</a>
			</th>
        </tr>

        @foreach ($players as $player)
            @php $profile = $player->profile; @endphp
            @if (!$player->hide)
            <tr>
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
            @endif
        @endforeach
    </table>
    </div>
</div>
@endsection