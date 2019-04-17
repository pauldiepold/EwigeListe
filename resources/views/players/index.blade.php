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
        <tr class="border-bottom-thick" style="line-height:120%;">
			<th>
				@php //$orderBy == 'surname' ? ($order == 'up' ? 'up' : 'down') : 'down' @endphp
            	<a href="/players/surname/{{ $orderBy == 'surname' ? ($order == 'up' ? 'down' : 'up') : 'down'}}"
				   class="{{ $orderBy == 'surname' ? 'text-primary'  : ''}}">
					Name
					<br class="d-inline d-sm-none">
					<i class="fas fa-sort{{ $orderBy == 'surname' ? '-' . $order . ' text-primary' : '' }}"></i>						
				</a>
			</th>
            <th>
				<a href="/players/games/{{ $orderBy == 'games' ? ($order == 'up' ? 'down' : 'up') : 'down' }}"
				   class="{{ $orderBy == 'games' ? 'text-primary'  : ''}}">
					Spiele
					<br class="d-inline d-sm-none">
					<i class="fas fa-sort{{ $orderBy == 'games' ? '-' . $order . ' text-primary' : '' }}"></i>
				</a>
			</th>
            <th>
				<a href="/players/points/{{ $orderBy == 'points' ? ($order == 'up' ? 'down' : 'up') : 'down' }}"
				   class="{{ $orderBy == 'points' ? 'text-primary'  : ''}}">
					Punkte
					<br class="d-inline d-sm-none">
					<i class="fas fa-sort{{ $orderBy == 'points' ? '-' . $order . ' text-primary' : '' }}"></i>
				</a>
			</th>
            <th>
				<a href="/players/pointsPerGame/{{ $orderBy == 'pointsPerGame' ? ($order == 'up' ? 'down' : 'up') : 'down' }}"
				   class="{{ $orderBy == 'pointsPerGame' ? 'text-primary'  : ''}}">
					Schnitt
					<br class="d-inline d-sm-none">
					<i class="fas fa-sort{{ $orderBy == 'pointsPerGame' ? '-' . $order . ' text-primary' : '' }}"></i>
				</a>
			</th>
            <th>
				<a href="/players/soli/{{ $orderBy == 'soli' ? ($order == 'up' ? 'down' : 'up') : 'down' }}"
				   class="{{ $orderBy == 'soli' ? 'text-primary'  : ''}}">
					Soli
					<br class="d-inline d-sm-none">
					<i class="fas fa-sort{{ $orderBy == 'soli' ? '-' . $order . ' text-primary' : '' }}"></i>
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