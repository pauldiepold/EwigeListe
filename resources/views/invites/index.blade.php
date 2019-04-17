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

        <div class="card mx-auto my-3" style="width: 18rem;">
            <div class="card-header font-weight-bold">
                Einladungs-PIN
            </div>
            <div class="card-body">
                <h4 class="card-title  font-weight-bold">{{ $invite->pin }}</h4>
                Gültig bis: {{ date("H:i \U\h\\r \a\m j.n.Y",  strtotime($invite->valid_until)) }}
            </div>
            <div class="card-footer py-1">
                <form action="/invites/{{ $invite->id }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-danger btn btn-link"><i class="fas fa-trash fa-lg"></i></button>
                </form>
            </div>
        </div>

    @endforeach



@endsection