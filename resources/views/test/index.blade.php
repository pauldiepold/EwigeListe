@extends('layouts.main')

@section('title', 'Testscript')

@section('heading', 'Textscript')

@section('content')

    <input type="text" id="input" v-model="message">

    <p>The value of the input is: @{{ message}}</p>

    <br><br>
    <ul>
        <li v-for="name in names" v-text="name"></li>
    </ul>

    <br><br>

    @php
        echo date('n');
    @endphp
@endsection