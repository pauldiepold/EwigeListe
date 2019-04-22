@extends('layouts.main')

@section('title', 'Testscript')

@section('heading', 'Textscript')

@section('content')

    <div id="app">
        <input type="text" id="input" v-model="message">
    </div>
    @{{ message}}
    <p>The value of the input is:

    </p>

    <br><br>
    <ul>
        <li v-for="name in names">@{{ name }}</li>
    </ul>

@endsection