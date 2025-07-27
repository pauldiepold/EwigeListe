@extends('layouts.main')

@section('title', 'Neuen User registrieren')

@section('heading', 'Neuen User registrieren')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm col-md-11 col-lg-8">

                <form method="POST" action="{{ route('register.quick.store') }}">
                    @csrf

                    <div class="form-group row">                        
                        <div class="col-sm-6 offset-sm-5 alert alert-info">
                            <i class="fas fa-info-circle tw-mr-2"></i>
                            Hier kannst du einen neuen User registrieren.
                            Wenn eine korrekte E-Mail-Adresse angegeben wird, kann das Passwort später zurückgesetzt werden.
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="surname"
                               class="col-sm-5 col-form-label text-sm-right">Vorname</label>

                        <div class="col-sm-6">
                            <input id="surname" type="text"
                                   class="form-control{{ $errors->has('surname') ? ' is-invalid' : '' }}"
                                   name="surname"
                                   value="{{ old('surname') }}" required autofocus>

                            @if ($errors->has('surname'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('surname') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="name" class="col-sm-5 col-form-label text-sm-right">Nachname</label>

                        <div class="col-sm-6">
                            <input id="name" type="text"
                                   class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                   name="name"
                                   value="{{ old('name') }}" required>

                            @if ($errors->has('name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="email"
                               class="col-sm-5 col-form-label text-sm-right">E-Mail</label>

                        <div class="col-sm-6">
                            <input id="email" type="email"
                                   class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                   name="email"
                                   value="{{ old('email') }}" required>

                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-sm-6 offset-sm-5">
                            <button type="submit" class="btn btn-primary mt-2">
                                <i class="fas fa-user-plus tw-mr-1"></i>
                                User registrieren
                            </button>
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-sm-6 offset-sm-5">
                            <a href="{{ route('rounds.create') }}" class="btn btn-secondary mt-2 ml-2">
                                <i class="fas fa-arrow-left tw-mr-1"></i>
                                Zurück zur Runderstellung
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection 