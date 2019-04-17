@extends('layouts.main')

@section('title', 'Einladungen')

@section('heading', 'Einladungen')

@section('content')

@include('include.error')

<form method="POST" action="/invites">
	@csrf
	<button class="btn btn-primary my-2" type="submit">Neue Einladungs-PIN erstellen</button>
</form>

@foreach ($invites as $invite)

<div class="card mx-auto my-2" style="width: 14rem;">
	<div class="card-header">
		Einladungs-PIN
	</div>
  <div class="card-body">
    <h5 class="card-title">{{ $invite->pin }}</h5>
	  G&uuml;ltig bis: {{ date("j.n. - H:i",  strtotime($invite->valid_until)) }}
  </div>
</div>

@endforeach



@endsection