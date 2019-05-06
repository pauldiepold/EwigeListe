@extends('layouts.main')

@section('title')
@yield('message')
@endsection

@section('content')
<div class="d-flex justify-content-center py-5">
	<div class="px-2 mt-3 h2 align-self-center" style="border-right: 2px solid;">
		@yield('code')
	</div>
	<div class="px-2 mt-3 h5 align-self-center">
		@yield('message')
	</div>
</div>
@endsection
