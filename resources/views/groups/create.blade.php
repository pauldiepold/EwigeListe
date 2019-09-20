@extends('layouts.main')

@section('title', 'Gruppen')

@section('heading', 'Gruppen')

@section('content')

    <form method="POST" action="/groups">
        @csrf
        <div class="form-group">
            <label for="name">Email address</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Gruppenname">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

@endsection
