@extends('layouts.main')

@section('title', 'Liste erstellen')

@section('heading', 'Liste erstellen')

@section('content')


    <form method="POST" action="/groups">
        @csrf
        <div class="form-group">
            <label for="name">Name der Liste:</label>
            <input type="text" class="form-control tw-max-w-sm tw-mx-auto" id="name" name="name"
                   placeholder="Listenname">
        </div>
        <button type="submit" class="btn btn-primary">Liste erstellen</button>
    </form>

@endsection
